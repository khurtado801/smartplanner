var lessionApp = angular.module('lessionModule', ['ngRoute']);
lessionApp.config(function($routeProvider) {
	$routeProvider
		.when('/lession/create', {
			templateUrl : 'public/app/templates/site/lession/create.html',
			controller  : 'lessionController'
		})	
			
});

