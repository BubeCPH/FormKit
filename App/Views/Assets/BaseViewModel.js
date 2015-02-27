function BaseViewModel() {
    self = this;
    self.URI = 'http://test.kalna.dk/KalnaBase/API/time/v1/settings/test';
    self.token = ko.observable();
    self.auth = ko.observable();
    self.username = ko.observable();
    self.password = ko.observable();

    self.ajax = function(uri, method, data) {
        var request = {
            url: uri,
            type: method,
            contentType: "application/json",
            accepts: "application/json",
            cache: false,
            dataType: 'json',
            data: JSON.stringify(data)
        };
        return $.ajax(request);
    };

    self.beginAdd = function() {
        alert("Add");
    };
    self.beginEdit = function(task) {
        alert("Edit: " + task.description());
    };
    self.remove = function(task) {
        alert("Remove: " + task.description());
    };


    self.ajax(self.URI, 'GET').done(function(data) {
        for (var i = 0; i < data.length; i++) {
            self.settings.push({
                id: ko.observable(data[i].Id),
                name: ko.observable(data[i].Name),
                description: ko.observable(data[i].Description),
                longDescription: ko.observable(data[i].LongDescription),
                type: ko.observable(data[i].Type),
                defaultValue: ko.observable(data[i].DefaultValue),
                formula: ko.observable(data[i].Formula)
            });
        }
    });
}
