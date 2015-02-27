//wrapper for an observable that protects value until committed
ko.protectedObservable = function (initalValue) {
    //private variables
    var _temp = ko.observable(initalValue);
    var _actual = ko.observable(initalValue);

    var result = ko.dependentObservable({
        read: function () {
            return _actual();
        },
        write: function (newValue) {
            _temp(newValue);
        }
    });

    //commit the temporary value to our observable, if it is different
    result.commit = function () {
        if (_temp() !== _actual()) {
            _actual(_temp());
        }
    };

    //notify subscribers to update their value with the original
    result.reset = function () {
        _actual.valueHasMutated();
        var temp = _temp();
        var actual = _actual();
        _temp(_actual());
        var temp = _temp();
        var actual = _actual();
    };

    // notify subscriber if value has changed
    result.isDirty = function () {
        return _temp() !== _actual();
    };
    return result;
};