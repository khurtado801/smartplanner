var registerPageApp = angular.module('registerPageModule', ['ngRoute']);
registerPageApp.config(function($routeProvider) {
	$routeProvider
		.when('/register', {
			templateUrl : 'public/app/templates/site/auth/register.html',
			controller  : 'registerController'
		})
		 		
});

