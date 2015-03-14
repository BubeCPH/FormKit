$(function(){
    var serviceName = '/FormKit/App/API/';
    var dataservice = new DataService(serviceName);
    var vm = new ViewModel(dataservice);
    vm.activate();
    ko.applyBindings(vm);
});