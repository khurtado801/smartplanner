var outlineModule = angular.module('outlinePageModule', ['ngRoute']);
outlineModule.config(function($routeProvider) {
	$routeProvider
		.when('/outline/create', {
			templateUrl : 'public/app/templates/site/outline/create.html',			
		})
			
});

