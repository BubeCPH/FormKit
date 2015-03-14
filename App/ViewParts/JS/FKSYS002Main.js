var FKSYS002DataService = function (serviceName) {
    var self = this;
    self.manager = new breeze.EntityManager(serviceName);
    self.getDataTypes = function (where, fetchStrategy) {
        fetchStrategy = fetchStrategy || breeze.FetchStrategy.FromServer;
        console.log('fetchStrategy: ' + fetchStrategy);
        var query = new breeze.EntityQuery()
                .from('DataTypes');
//                .expand('products');
        if (where) {
            query = query.where(where);
        }


        var test = query.using(self.manager).using(fetchStrategy).execute();

        console.log('execute: ' + test);
        return test;
    };

    self.createDataType = function (datatype) {
        datatype = datatype || {};
        return self.manager.createEntity('DataType',
                {name: ko.unwrap(datatype.name), description: ko.unwrap(datatype.description)});
    };

    self.removeDataType = function (datatype) {
        datatype.entityAspect.setDeleted();
    };

    self.saveChanges = function () {
        return self.manager.saveChanges();
    };

    self.findDataTypeById = function (datatypeId) {
        var deferred = Q.defer();
        self.getDataTypes(new breeze.Predicate('id', '==', datatypeId),
                breeze.FetchStrategy.FromLocalCache).then(function (data) {
            deferred.resolve(data.results[0]);
        });
        return deferred.promise;
    };
};

var FKSYS002ViewModel = function (dataservice) {
    var self = this;
//    console.log(dataservice);
//    var serviceName = '/FormKit/App/API/';
//    var dataservice = new FKSYS002DataService(serviceName);
    self.datatypes = ko.observableArray([]);
    self.activeDataType = ko.observable();
    self.newDataType = {
        name: ko.observable(),
        datatype: ko.observable(),
        description: ko.observable()
    };

    self.activateDataType = function (datatypeId) {
        if (!datatypeId) {
            self.activeDataType(null);
            return;
        }
        dataservice.findDataTypeById(datatypeId).then(function (datatype) {
            self.activeDataType(datatype);
        });
    };

    self.isActiveDataType = function (datatype) {
        return datatype === self.activeDataType();
    };

    self.fetchDataTypes = function () {
        console.log('fetchDataTypes');
        dataservice.getDataTypes().then(function (data) {
            console.log(data);
            self.datatypes(data.results);
        });
    };

    self.removeActiveDataType = function () {
        if (!confirm('Are you sure to remove the datatype?')) {
            return;
        }
        var datatype = self.activeDataType();
        dataservice.removeDataType(datatype);
        self.datatypes.remove(datatype);
        self.activeDataType(null);
    };

    self.saveChanges = function () {
        if (ko.unwrap(self.newDataType.name)) {
            var datatype = dataservice.createDataType(self.newDataType);
            self.newDataType.name('');
            self.newDataType.description('');
            self.datatypes.push(datatype);
        }
        dataservice.saveChanges().then(function (data) {
            alert('success');
        }, function (error) {
            console.log(error);
            console.log(error.stack);
            if (error.entityErrors) {
                for (var i = 0; i < error.entityErrors.length; i++) {
                    var e = error.entityErrors[i];
                    alert((e.isServerError ? '(SERVER ERROR)' : '(CLIENT ERROR)')
                            + ' ' + e.errorMessage);
                }
            }
            else {
                alert('Save failed: ' + error);
            }
        });
    };

    self.fetchDataTypes();

    self.activate = function (datatypeId) {
        self.activateDataType(datatypeId);
    };

};

//$(function(){
//    var serviceName = '/FormKit/App/API/';
//    var dataservice = new FKSYS002DataService(serviceName);
//    var FKSYS002VM = new FKSYS002ViewModel(dataservice);
//    FKSYS002VM.activate();
//    ko.applyBindings(FKSYS002VM, $('#window_FKSYS002')[0]);
//});