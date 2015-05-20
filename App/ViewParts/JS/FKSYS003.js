function Datatype(id, name, datatype, description) {
    var me = this;
    id = (id === 'undefined' ? null : id);
    name = (name === 'undefined' ? null : name);
    datatype = (datatype === 'undefined' ? null : datatype);
    description = (description === 'undefined' ? null : description);
    me.id = ko.observable(id);
    me.name = ko.protectedObservable(name);
    me.datatype = ko.protectedObservable(datatype);
    me.description = ko.protectedObservable(description);
    me.dirtyFlag = ko.computed(function () {
        return me.name.isDirty() || me.datatype.isDirty() || me.description.isDirty();
    });
    me.deleteFlag = ko.observable(false);
    me.remove = function () {
        me.deleteFlag(true);
    };
    me.commit = function () {
        me.name.commit();
        me.datatype.commit();
        me.description.commit();
//                            self.update(me);
        me.deleteFlag(false);
    };
    me.reset = function () {
        me.name.reset();
        me.name.notifySubscribers();
        me.datatype.reset();
        me.datatype.notifySubscribers();
        me.description.reset();
        me.description.notifySubscribers();
        me.deleteFlag(false);
    };
}

function FKSYS003_ViewModel(initialData, searchmode) {
    var self = this;
    self.searchmode = searchmode;
    self.datatypes = ko.observableArray();
    self.selectedId = ko.observable(0);
    self.selectedItem = ko.computed(function () {
        return ko.utils.arrayFirst(self.datatypes()
                , function (item) {
                    return item.id() === self.selectedId();
                });
    });

    var temp_id = -1;
    self.add = function () {
        self.datatypes.push(new Datatype(temp_id));
        temp_id--;
    };
    self.remove = function () {
        if (self.selectedId() > 0) {
            var selectedItem = ko.utils.arrayFirst(self.datatypes(), function (item) {
                return item.id() === self.selectedId();
            });
            selectedItem.remove();
        }
    };
    self.save = function () {
        ko.utils.arrayForEach(self.datatypes(), function (item) {
            if (item.dirtyFlag()) {
                item.commit();
            }
        });
    };
    self.refetch = function () {
        self.fetchDatatypes();
    };
    self.executeSearch = function () {
        ko.utils.arrayForEach(self.datatypes(), function (item) {
            item.commit();
        });
    };

    self.fetchDatatypes = function (initialData) {
        if (typeof initialData !== 'undefined') {
            self.fillDatatypes(initialData);
        } else {
            URI = 'http://localhost/FormKit/App/API/dataTypes';
            ajaxGet(URI).done(function (data) {
                self.fillDatatypes(data);
            });
        }
//                                appendTooltips();
    };
    self.fillDatatypes = function (data) {
        self.datatypes.removeAll();
        for (var i = 0; i < data.length; i++) {
            self.datatypes.push(new Datatype(data[i].id, data[i].name, data[i].datatype, data[i].description));
        }
//                            self.datatypes.push(new Datatype(null, null, null, null));
//                                appendTooltips();
    };
    self.dirtyItems = ko.computed(function () {
        return ko.utils.arrayFilter(self.datatypes(), function (item) {
            return item.dirtyFlag();
        });
    }, self);
    self.isDirty = ko.computed(function () {
        return self.dirtyItems().length > 0;
    }, self);

    self.deletedItems = ko.computed(function () {
        return ko.utils.arrayFilter(self.datatypes(), function (item) {
            return item.deleteFlag();
        });
    }, self);
    self.containsDeleted = ko.computed(function () {
        return self.deletedItems().length > 0;
    }, self);
//self.isDirty = FALSE;
    if (searchmode === 'undefined' || !searchmode) {
        self.fetchDatatypes(initialData);
    } else if (searchmode) {
        self.datatypes.push(new Datatype());
    }
    ;
}

function FKSYS003_MasterViewModel() {
    var master = this;
    master.vm = ko.observable(null);
    master.svm = ko.observable(null);
    var init; // = '[{"id":"1","name":"String","datatype":"VARCHAR(4000)","description":""},{"id":"2","name":"Text","datatype":"TEXT","description":""},{"id":"3","name":"Number","datatype":"DECIMAL(15,4)","description":""},{"id":"4","name":"Boolean","datatype":"BIT(1)","description":""},{"id":"5","name":"Time","datatype":"TIME","description":""},{"id":"6","name":"Date","datatype":"DATE","description":""}]';

    master.FKSYS003_vm = new FKSYS003_ViewModel(init);
    master.FKSYS003_svm = new FKSYS003_ViewModel(init, true);
    master.initSearch = function () {
        master.vm(master.FKSYS003_svm);
    };
    master.executeSearch = function () {
        master.vm().executeSearch();
    };
    master.redoSearch = function () {
//                            master.vm(JSON.parse(master.svm()));
    };
    master.cancelSearch = function () {
        master.vm(master.FKSYS003_vm);
    };

    master.vm(master.FKSYS003_vm);
}
//var FKSYS003_mvm = new FKSYS003_MasterViewModel;
//ko.applyBindings(FKSYS003_mvm, $('#window_FKSYS003')[0]);