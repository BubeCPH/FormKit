function changeTracker(objectToTrack, hashFunction) {
    hashFunction = hashFunction || ko.toJSON;
    var lastCleanState = ko.observable(hashFunction(objectToTrack));

    var result = {
        dirty: ko.dependentObservable(function () {
            return hashFunction(objectToTrack) !== lastCleanState();
        }),
        markCurrentStateAsClean: function () {
            lastCleanState(hashFunction(objectToTrack));
        }
    };

    return function () {
        return result;
    };
}

function Datatype(id, name, datatype, description) {
    var obj = this;
    id = (id === 'undefined' ? null : id);
    name = (name === 'undefined' ? null : name);
    datatype = (datatype === 'undefined' ? null : datatype);
    description = (description === 'undefined' ? null : description);
    obj.id = ko.observable(id);
    obj.name = ko.observable(name);
    obj.datatype = ko.observable(datatype);
    obj.description = ko.observable(description);
}

function Fieldtype(id, name) {
    var obj = this;
    id = (id === 'undefined' ? null : id);
    name = (name === 'undefined' ? null : name);
//    templates = (templates === 'undefined' ? null : templates);
//    obj.currentTemplate = ko.observable();
//    obj.setCurrentTemplate = function (item) {
//        alert(item.html_template);
//        obj.currentTemplate(item);
//    };
    obj.id = ko.observable(id);
    obj.name = ko.observable(name);
//    obj.templates = ko.observableArray(templates);
//    alert(name);

//    var temp_id = -1;
//    obj.addTemplate = function () {
//        obj.templates.push(new Template(temp_id));
//        temp_id--;
//    };
//    obj.removeTemplate = function () {
//        if (self.selectedId() > 0) {
//            var selectedItem = ko.utils.arrayFirst(self.datatypes(), function (item) {
//                return item.id() === self.selectedId();
//            });
//            selectedItem.remove();
//        }
//    };

    obj.dirtyFlag = new changeTracker(obj);
//    obj.dirtyFlag = ko.computed(function () {
//        return obj.name.isDirty();
//    });
    obj.deleteFlag = ko.observable(false);
    obj.remove = function () {
        obj.deleteFlag(true);
    };
//    obj.commit = function () {
//        obj.name.commit();
//        obj.deleteFlag(false);
//    };
//    obj.reset = function () {
//        obj.name.reset();
//        obj.name.notifySubscribers();
//        obj.deleteFlag(false);
//    };
}
;

function Template(dattyp_id, fldtyp_id, html_template) {
    var obj = this;
    dattyp_id = (dattyp_id === 'undefined' ? null : dattyp_id);
    fldtyp_id = (fldtyp_id === 'undefined' ? null : fldtyp_id);
    html_template = (html_template === 'undefined' ? null : html_template);
    obj.dattyp_id = ko.observable(dattyp_id);
    obj.fldtyp_id = ko.observable(fldtyp_id);
    obj.html_template = ko.observable(html_template);
    obj.dirtyFlag = new changeTracker(obj);
//            ko.computed(function () {
//        return obj.dattyp_id.isDirty() || obj.fldtyp_id.isDirty() || obj.html_template.isDirty();
//    });
    obj.deleteFlag = ko.observable(false);
    obj.remove = function () {
        obj.deleteFlag(true);
    };
//    obj.commit = function () {
//        obj.dattyp_id.commit();
//        obj.fldtyp_id.commit();
//        obj.html_template.commit();
////                            self.update(me);
//        obj.deleteFlag(false);
//    };
//    obj.reset = function () {
//        obj.dattyp_id.reset();
//        obj.dattyp_id.notifySubscribers();
//        obj.fldtyp_id.reset();
//        obj.fldtyp_id.notifySubscribers();
//        obj.html_template.reset();
//        obj.html_template.notifySubscribers();
//        obj.deleteFlag(false);
//    };
}
;

function FKSYS001_ViewModel(initialData, searchmode) {
    var self = this;
    self.searchmode = searchmode;

    self.datatypes = ko.observableArray();
    self.fieldtypes = ko.observableArray();
    self.templates = ko.observableArray();

    self.currentDatatype = ko.observable();
    self.setCurrentDatatype = function (item) {
        self.currentDatatype(item);
    };
    self.activeFieldtype = ko.observable(null);
    self.setActiveFieldtype = function (record) {
        self.activeFieldtype(ko.unwrap(record));
//        console.log('currentTemplates().length: ' + self.currentTemplates().length);
        if (self.currentTemplates().length === 0) {
            self.fetchTemplates(self.activeFieldtype().id());
        }
//        console.log(self.activeFieldtype().id());
        return true;
    };
    self.isActiveFieldtype = function (record) {
        return self.activeFieldtype() === ko.unwrap(record);
    };
    self.activeTemplate = ko.observable(null);
    self.setActiveTemplate = function (record) {
        self.activeTemplate(ko.unwrap(record));
        return true;
    };
    self.isActiveTemplate = function (record) {
        return self.activeTemplate() === ko.unwrap(record);
    };
    self.currentTemplates = ko.computed(function () {
        if (self.activeFieldtype() !== null && typeof self.activeFieldtype() !== "undefined") {
            var activeFieldtypeId = self.activeFieldtype().id();
//            console.log('activeFieldtypeId: ' + activeFieldtypeId);
//            console.log('self.templates().length: ' + self.templates().length);
            var filteredCollection = ko.utils.arrayFilter(self.templates(), function (template) {
//                console.log('template.fldtyp_id: ' + template.fldtyp_id());
                return template.fldtyp_id() === activeFieldtypeId;
            });
        }
        return filteredCollection;
    });

    self.selectedId = ko.observable(0);

    self.fetchFieldtypes = function () {
        var URI = 'http://localhost/FormKit/App/API/fieldTypes';
//        console.log(URI);
        ajaxGet(URI).always(function (data, textStatus, jqXHR) {
//            self.fillFieldtypes(data);
            self.fieldtypes.removeAll();
            for (var i = 0; i < data.length; i++) {
                var fieldtype = new Fieldtype(data[i].id, data[i].name, null);
//            for (var ni = 0; ni < data[i].templates.length; ni++) {
//                fieldtype.templates.push(new Template(data[i].templates[ni].data_types_id, data[i].templates[ni].field_types_id, data[i].templates[ni].html_templates));
//            }
//            if (data[i].templates.length === 0) {
//                fieldtype.templates.push(new Template(null, null, null));
//            }
                self.fieldtypes.push(fieldtype);
            }
        });
    };

    self.fetchTemplates = function (parentId) {
        var URI = 'http://localhost/FormKit/App/API/fieldTypes/' + parentId + '/fieldTemplates';
//        console.log(URI);
        ajaxGet(URI).always(function (data, textStatus, jqXHR) {
//            self.templates.removeAll();
//            console.log(textStatus);
//            console.log(jqXHR);
            if (jqXHR.status === 204) {
                self.templates.push(new Template(null, parentId, null));
            } else {
                for (var i = 0; i < data.length; i++) {
//                    console.log('data[i].fldtyp_id: ' + data[i].fldtyp_id);
                    self.templates.push(new Template(data[i].dattyp_id, data[i].fldtyp_id, data[i].html_template));//Fieldtype(data[fieldtype].id, data[fieldtype].name, null));
                }
            }
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
    };
    self.fillDatatypes = function (data) {
        self.datatypes.removeAll();
        for (var i = 0; i < data.length; i++) {
            self.datatypes.push(new Datatype(data[i].id, data[i].name, data[i].datatype, data[i].description));
        }
    };

    self.dirtyFieldtypes = ko.computed(function () {
        return ko.utils.arrayFilter(self.fieldtypes(), function (item) {
            console.log('dirtyFieldtypes: ' + item.dirtyFlag().dirty());
            return item.dirtyFlag();
        });
    }, self);
    self.dirtyTemplates = ko.computed(function () {
        return ko.utils.arrayFilter(self.templates(), function (item) {
            console.log('dirtyTemplates: ' + item.dirtyFlag().dirty());
            return item.dirtyFlag();
        });
    }, self);
    self.isDirty = ko.computed(function () {
        if (self.dirtyFieldtypes().length > 0 || self.dirtyTemplates().length > 0) {
            console.log('isDirty: true');
            return true;
        } else {
            console.log('isDirty: false');
            return false;
        }
//        return self.dirtyFieldtypes().length > 0 || self.dirtyTemplates().length > 0;
    }, self);

    self.deletedFieldtypes = ko.computed(function () {
        return ko.utils.arrayFilter(self.fieldtypes(), function (item) {
            return item.deleteFlag();
        });
    }, self);
    self.deletedTemplates = ko.computed(function () {
        return ko.utils.arrayFilter(self.templates(), function (item) {
            return item.deleteFlag();
        });
    }, self);
    self.containsDeleted = ko.computed(function () {
        return self.deletedFieldtypes().length > 0 || self.deletedTemplates().length > 0;
    }, self);

    self.fetchDatatypes(initialData);

    self.fetch = function (bool) {
        if (bool) {
            self.fieldtypes.removeAll();
            self.fetchFieldtypes(initialData);
            self.templates.removeAll();
            self.fetchTemplates();
        }
    };
    
    if (searchmode === 'undefined' || !searchmode) {
        self.fetch(true);
    } else if (searchmode) {
        self.fieldtypes.push(new Fieldtype());
    }
}

function FKSYS001_MasterViewModel() {
    var master = this;
    master.vm = ko.observable(null);
    master.svm = ko.observable(null);
    var init; // = '[{"id":"1","name":"String","datatype":"VARCHAR(4000)","description":""},{"id":"2","name":"Text","datatype":"TEXT","description":""},{"id":"3","name":"Number","datatype":"DECIMAL(15,4)","description":""},{"id":"4","name":"Boolean","datatype":"BIT(1)","description":""},{"id":"5","name":"Time","datatype":"TIME","description":""},{"id":"6","name":"Date","datatype":"DATE","description":""}]';

    master.FKSYS001_vm = new FKSYS001_ViewModel(init);
    master.FKSYS001_svm = new FKSYS001_ViewModel(init, true);
    master.initSearch = function () {
        master.vm(master.FKSYS001_svm);
    };
    master.redoSearch = function () {
//                            master.vm(JSON.parse(master.svm()));
    };
    master.cancelSearch = function () {
        master.vm(master.FKSYS001_vm);
    };

    master.add = function () {
        master.vm().datatypes.push(new Datatype());
    };
    master.remove = function () {
        if (master.vm().selectedId() > 0) {
            var selectedItem = ko.utils.arrayFirst(master.vm().datatypes(), function (item) {
                return item.id() === master.vm().selectedId();
            });
            selectedItem.remove();
        }
    };
    master.save = function () {
    };
    master.refetch = function () {
        master.vm().fetch(true);
    };
    master.executeSearch = function () {
        ko.utils.arrayForEach(master.vm().datatypes(), function (item) {
            item.commit();
        });
    };

    master.vm(master.FKSYS001_vm);
}
//var FKSYS001_mvm = new FKSYS001_MasterViewModel;
//ko.applyBindings(FKSYS001_mvm, $('#window_FKSYS001')[0]);