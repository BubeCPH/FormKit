var FKSYS002DataService = function (serviceName) {
    var self = this;
    self.manager = new breeze.EntityManager(serviceName);
    self.getDatatypes = function (where, fetchStrategy) {
        fetchStrategy = fetchStrategy || breeze.FetchStrategy.FromServer;
        var query = new breeze.EntityQuery()
                .from('Datatypes');
//                .expand('products');
        if (where) {
            query = query.where(where);
        }

        return query.using(self.manager).using(fetchStrategy).execute();
    };

    self.createDatatype = function (datatype) {
        datatype = datatype || {};
        return self.manager.createEntity('Datatype',
                {name: ko.unwrap(datatype.name), description: ko.unwrap(datatype.description)});
    };

    self.removeDatatype = function (datatype) {
        datatype.entityAspect.setDeleted();
    };

    self.saveChanges = function () {
        return self.manager.saveChanges();
    };

    self.findDatatypeById = function (datatypeId) {
        var deferred = Q.defer();
        self.getDatatypes(new breeze.Predicate('id', '==', datatypeId),
                breeze.FetchStrategy.FromLocalCache).then(function (data) {
            deferred.resolve(data.results[0]);
        });
        return deferred.promise;
    };
};