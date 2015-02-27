define(['jquery', 'knockout', 'knockout-projections', './knockout.protectedObservable', './knockout.ajax', './router', 'bootstrap', './jquery.desktop'], function ($, ko, router) {

    // Components can be packaged as AMD modules, such as the following:
    ko.components.register('nav-bar', {require: 'components/nav-bar/nav-bar'});
    ko.components.register('home-page', {require: 'components/home-page/home'});

    // ... or for template-only components, you can just point to a .html file directly:
    ko.components.register('about-page', {
        template: {require: 'text!components/about-page/about.html'}
    });

    function Datatype(id, name, datatype, description) {
        var me = this;
        me.id = ko.observable(id);
        me.name = ko.protectedObservable(name);
        me.datatype = ko.protectedObservable(datatype);
        me.description = ko.protectedObservable(description);

        me.dirtyFlag = ko.computed(function () {
            return me.startTime.isDirty() || me.endTime.isDirty() || me.description.isDirty();
        });
        me.deleteFlag = ko.observable(false);

        me.remove = function () {
            me.deleteFlag(true);
        };

        me.commit = function () {
            me.name.commit();
            me.datatype.commit();
            me.description.commit();
            self.update(me);
            me.deleteFlag(false);
        };
//
//                            me.commitOnEnter = function (data, event) {
//                                var keyCode = (event.which ? event.which : event.keyCode);
//                                if (keyCode === 13) {
//                                    me.commit();
//                                    return false;
//                                }
//                                return true;
//                            };

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
    function DataTypesViewModel() {
        var self = this;

        self.datatypes = ko.observableArray();

//                            self.addPerson = function () {
//                                self.people.push({name: "New at " + new Date()});
//                            };
//
//                            self.removePerson = function () {
//                                self.people.remove(this);
//                            };

        self.fetchDatatypes = function (initialData) {
            if (typeof initialData !== 'undefined') {
                self.fillDatatypes(initialData);
            } else {
                URI = 'http://localhost/FormKit/App/API/datatypes';
//                                    var now = new Date();
//                ajaxGet(URI, {date: self.date()}).done(function (data) {
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
            self.datatypes.push(new Datatype(null, null, null, null));
//                                appendTooltips();
        };

        self.fetchDatatypes();

    }

    var dataTypesViewModel = new DataTypesViewModel();
    ko.applyBindings(dataTypesViewModel);
//    ko.applyBindings(dataTypesViewModel, $('#window_data_types'));



    // [Scaffolded component registrations will be inserted here. To retain this feature, don't remove this comment.]

    // Start the application
//    ko.applyBindings({route: router.currentRoute});
});
