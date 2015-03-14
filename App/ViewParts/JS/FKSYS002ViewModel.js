var FKSYS002ViewModel = function (dataservice) {
    var self = this;
    self.datatypes = ko.observableArray([]);
    self.activeDatatype = ko.observable();
    self.newDatatype = {
        name: ko.observable(),
        datatype: ko.observable(),
        description: ko.observable()
    };

    self.activateDatatype = function (datatypeId) {
        if (!datatypeId) {
            self.activeDatatype(null);
            return;
        }
        dataservice.findDatatypeById(datatypeId).then(function (datatype) {
            self.activeDatatype(datatype);
        });
    };

    self.isActiveDatatype = function (datatype) {
        return datatype === self.activeDatatype();
    };

    self.fetchDatatypes = function () {
        dataservice.getDatatypes().then(function (data) {
            self.datatypes(data.results);
        });
    };

    self.removeActiveDatatype = function () {
        if (!confirm('Are you sure to remove the datatype?')) {
            return;
        }
        var datatype = self.activeDatatype();
        dataservice.removeDatatype(datatype);
        self.datatypes.remove(datatype);
        self.activeDatatype(null);
    };

    self.saveChanges = function () {
        if (ko.unwrap(self.newDatatype.name)) {
            var datatype = dataservice.createDatatype(self.newDatatype);
            self.newDatatype.name('');
            self.newDatatype.description('');
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

    self.activate = function (datatypeId) {
        self.activateDatatype(datatypeId);
    };

    self.fetchDatatypes();

};