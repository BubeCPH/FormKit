app = {};

app.dataservice = (function (breeze, logger) {

    var serviceName = '/FormKit/App/API/'; // route to the same origin Web Api controller

    // *** Cross origin service example  ***
    // controller in different origin
    //var serviceName = 'http://sampleservice.breezejs.com/api/todos/'; 

    var manager = new breeze.EntityManager(serviceName);
//    manager.enableSaveQueuing(true);
    addTodoProperties();

    return {
        addPropertyChangeHandler: addPropertyChangeHandler,
        createTodo: createTodo,
        deleteTodoAndSave: deleteTodoAndSave,
        getTodos: getTodos,
        hasChanges: hasChanges,
        purge: purge,
        reset: reset,
        saveChanges: saveChanges
    };

    /*** implementation details ***/

    function addPropertyChangeHandler(handler) {
        // call handler when an entity property of any entity changes
        return manager.entityChanged.subscribe(function (changeArgs) {
            var action = changeArgs.entityAction;
            if (action === breeze.EntityAction.PropertyChange) {
                handler(changeArgs);
            }
        });
    }

    function addTodoProperties() {
        // untracked 'isEditing' property to the 'TodoItem' type
        // see http://www.breezejs.com/sites/all/apidocs/classes/MetadataStore.html#method_registerEntityTypeCtor
        var metadataStore = manager.metadataStore;
        metadataStore.registerEntityTypeCtor('TodoItem', null, todoInit);

        function todoInit(todo) {
            todo.isEditing = ko.observable(false);
        }
    }

    function createTodo(initialValues) {
        return manager.createEntity('TodoItem', initialValues);
    }

    function deleteTodoAndSave(todoItem) {
        if (todoItem) {
            var aspect = todoItem.entityAspect;
            if (aspect.isBeingSaved && aspect.entityState.isAdded()) {
                // wait to delete added entity while it is being saved  
                setTimeout(function () { deleteTodoAndSave (todoItem); }, 100);
                return;          
            } 
            aspect.setDeleted();
            saveChanges();
        }
    }

    function getTodos(includeArchived) {
        var query = breeze.EntityQuery
                .from("Todos")
                .orderBy("CreatedAt");

        if (!includeArchived) { // exclude archived Todos
            // add filter clause limiting results to non-archived Todos
            query = query.where("IsArchived", "==", false);
        }

        return manager.executeQuery(query);
    }
    
    function handleSaveValidationError(error) {
        var message = "Not saved due to validation error";
        try { // fish out the first error
            var firstErr = error.entityErrors[0];
            message += ": " + firstErr.errorMessage;
        } catch (e) { /* eat it for now */ }
        return message;
    }

    function hasChanges() {
        return manager.hasChanges();
    }

    function saveChanges() {
        return manager.saveChanges()
            .then(saveSucceeded)
            .fail(saveFailed);

        function saveSucceeded(saveResult) {
            logger.success("# of Todos saved = " + saveResult.entities.length);
            logger.log(saveResult);
        }

        function saveFailed(error) {
            var reason = error.message;
            var detail = error.detail;

            if (error.entityErrors) {
                reason = handleSaveValidationError(error);
            } else if (detail && detail.ExceptionType &&
                detail.ExceptionType.indexOf('OptimisticConcurrencyException') !== -1) {
                // Concurrency error 
                reason =
                    "Another user, perhaps the server, " +
                    "may have deleted one or all of the todos." +
                    " You may have to restart the app.";
            } else {
                reason = "Failed to save changes: " + reason +
                         " You may have to restart the app.";
            }

            logger.error(error, reason);
            // DEMO ONLY: discard all pending changes
            // Let them see the error for a second before rejecting changes
            setTimeout(function () {
                manager.rejectChanges();
            }, 1000);
            throw error; // so caller can see it
        }
    }

    //#region demo operations
    function purge(callback) {
        return $.post(serviceName + '/purge')
        .then(function () {
            logger.success("database purged.");
            manager.clear();
            if (callback) callback();
        })
        .fail(function (error) {
            logger.error("database purge failed: " + error);
        });
    }

    function reset(callback) {
        return $.post(serviceName + '/reset')
        .then(function () {
            logger.success("database reset.");
            manager.clear();
            if (callback) callback();
        })
        .fail(function (error) {
            logger.error("database reset failed: " + error);
        });
    }
    //#endregion

})(breeze, app.logger);


app.logger = (function () {

    // This logger wraps the toastr logger and also logs to console
    // toastr.js is library by John Papa that shows messages in pop up toast.
    // https://github.com/CodeSeven/toastr

    toastr.options.timeOut = 2000; // 2 second toast timeout
    toastr.options.positionClass = 'toast-bottom-right';

    var logger = {
        error: error,
        info: info,
        success: success,
        warning: warning,
        log: log // straight to console; bypass toast
    };

    function error(message, title) {
        toastr.error(message, title);
        log("Error: " + message);
    };
    function info(message, title) {
        toastr.info(message, title);
        log("Info: " + message);
    };
    function success(message, title) {
        toastr.success(message, title);
        log("Success: " + message);
    };
    function warning(message, title) {
        toastr.warning(message, title);
        log("Warning: " + message);
    };

    // IE and google chrome workaround
    // http://code.google.com/p/chromium/issues/detail?id=48662
    function log() {
        var console = window.console;
        !!console && console.log && console.log.apply && console.log.apply(console, arguments);
    }

    return logger;
})();


app.viewModel = (function (logger, dataservice) {

    var vm = {
        addItem: addItem,
        archiveCompletedItems: archiveCompletedItems,
      //archiveCompletedMessage - see addComputed()
        deleteItem: deleteItem,
        editBegin: editBegin,
        editEnd: editEnd, 
        includeArchived: ko.observable(false),
        items: ko.observableArray(),
      //itemsLeftMessage - see addComputed()
      //markAllCompleted - see addComputed()
        newTodoDescription: ko.observable(""),
        purge: purge,
        reset: reset
    };

    var suspendSave = false;

    initVm();
    
    return vm; // done with setup; return module variable

    /* Implementation */

    function initVm() {
        vm.includeArchived.subscribe(getTodos);
        addComputeds();
        getTodos();

        // Listen for property change of ANY entity so we can (optionally) save
        dataservice.addPropertyChangeHandler(propertyChanged);
    }

    function addComputeds() {
        vm.archiveCompletedMessage = ko.computed(function () {
            var count = getStateOfItems().itemsDoneCount;
            if (count > 0) {
                return "Archive " + count + " completed item" + (count > 1 ? "s" : "");
            }
            return null;
        });

        vm.itemsLeftMessage = ko.computed(function () {
            var count = getStateOfItems().itemsLeftCount;
            if (count > 0) {
                return count + " item" + (count > 1 ? "s" : "") + " left";
            }
            return null;
        });

        vm.markAllCompleted = ko.computed({
            read: function () {
                var state = getStateOfItems();
                return state.itemsLeftCount === 0 && vm.items().length > 0;
            },
            write: function (value) {
                suspendSave = true;
                vm.items().forEach(function (item) {
                    item.IsDone(value);
                });
                suspendSave = false;
                save();
            }
        });
    }

    function archiveCompletedItems() {
        var state = getStateOfItems();
        suspendSave = true;
        state.itemsDone.forEach(function (item) {
            if (!vm.includeArchived()) {
                vm.items.remove(item);
            }
            item.IsArchived(true);
        });
        suspendSave = false;
        save();
    }

    function getTodos() {
        dataservice.getTodos(vm.includeArchived())
            .then(querySucceeded)
            .fail(queryFailed);

        function querySucceeded(data) {
            vm.items(data.results);
            logger.info("Fetched Todos " +
                (vm.includeArchived() ? "including archived" : "excluding archived"));
        }
        function queryFailed(error) {
            logger.error(error.message, "Query failed");
        }
    }

    function addItem() {
        var description = vm.newTodoDescription();
        if (!description) { return; }

        var item = dataservice.createTodo({
            Description: description,
            CreatedAt: new Date(),
            IsDone: vm.markAllCompleted()
        });

        save(true).catch(addFailed);
        vm.items.push(item);
        vm.newTodoDescription("");
        
        function addFailed() {
            var index = vm.items.indexOf(item);
            if (index > -1) {
                setTimeout(function () { vm.items.splice(index, 1); }, 2000);
            }        
        }
    }

    function editBegin(item) { item.isEditing(true); }

    function editEnd(item) { item.isEditing(false); }

    function deleteItem(item) {
        vm.items.remove(item);
        dataservice.deleteTodoAndSave(item);
    };

    function getStateOfItems() {
        var itemsDone = [], itemsLeft = [];

        vm.items().forEach(function (item) {
            if (item.IsDone()) {
                if (!item.IsArchived()) {
                    itemsDone.push(item); // only unarchived items                
                }
            } else {
                itemsLeft.push(item);
            }
        });

        return {
            itemsDone: itemsDone,
            itemsDoneCount: itemsDone.length,
            itemsLeft: itemsLeft,
            itemsLeftCount: itemsLeft.length
        };
    }

    function propertyChanged(changeArgs) {
        // propertyChanged triggers save attempt UNLESS the property is the 'Id'
        // because THEN the change is actually the post-save Id-fixup 
        // rather than user data entry so there is actually nothing to save.
        if (changeArgs.args.propertyName !== 'Id') {
            save();
        }
    }

    function purge() {
        return dataservice.purge(getTodos);
    }

    function reset() {
        return dataservice.reset(getTodos);
    }

    function save(force) {
        // Save if have changes to save AND
        // if must save OR save not suspended
        if (dataservice.hasChanges() && (force || !suspendSave)) {
            return dataservice.saveChanges();
        }
        // Decided not to save; return resolved promise w/ no result
        return Q(false);
    }   

})(app.logger, app.dataservice);

// Bind viewModel to view in index.html
ko.applyBindings(app.viewModel);