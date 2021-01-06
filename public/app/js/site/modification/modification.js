var ModificationModule = angular.module('ModificationPageModule', ['ngRoute']);
ModificationModule.config(function ($routeProvider) {
    $routeProvider
            .when('/modification/create', {
                templateUrl: 'public/app/templates/site/modification/create.html',
            })
            .when('/preview/create', {
                templateUrl: 'public/app/templates/site/preview/create.html',
            })
            .when('/modification/pdf', {
                templateUrl: 'public/app/templates/site/preview/pdf.html',
                controller: 'modificationController'
            })

});

