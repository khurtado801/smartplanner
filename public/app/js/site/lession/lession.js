var lessionApp = angular.module( 'lessionModule', [ 'ngRoute' ] );
lessionApp.config( function ( $routeProvider ) {
    $routeProvider
        .when( '/lession/create', {
            templateUrl: 'public/app/templates/site/lession/create.html',
            controller: 'lessionController'
        } )
        .when( '/lession/list_lessons', {
            templateUrl: 'public/app/templates/site/lession/list_lessons.html',
            controller: 'lessionController'
        } )
} );