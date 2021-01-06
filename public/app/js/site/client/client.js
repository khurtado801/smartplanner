var clientApp = angular.module('clientModule', ['ngRoute']);

clientApp.config(function($routeProvider) {
	$routeProvider
		.when('/client/profile/update', {
			templateUrl : 'public/app/templates/site/client/clientUpdate.html',
			controller  : 'clientController'
		})
		.when('/client/post/project', {
			templateUrl : 'public/app/templates/site/client/postProject.html',
			controller  : 'clientController'
		})
		.when('/client/dashboard', {
			templateUrl : 'public/app/templates/site/client/clientDashboard.html',
			controller  : 'clientController'
		})
});
