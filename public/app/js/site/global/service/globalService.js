var globalApp = angular.module('globalModule');
globalApp.factory('officeListFactory', function($http, $window) {    
    var factoryResult = {
        getOfficeList: function() {
            var promise = $http({
                method: 'GET', 
                url: 'api/home/homepagedata1' 
            }).success(function(data, status, headers, config) {
                console.log (data);
                return data;
            });

            return promise;
        }
    }; 

    console.log (factoryResult.getOfficeList());
    return factoryResult;
});
