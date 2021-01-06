var contactPageApp = angular.module('contactPageModule', ['ngRoute']);

contactPageApp.config(function($routeProvider) {
	$routeProvider
		.when('/contact', {
			templateUrl : 'public/app/templates/site/contact/contact.html',
			controller  : 'contactController'
		})
});

