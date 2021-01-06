var pageApp = angular.module('pageModule', ['ngRoute']);

pageApp.config(function($routeProvider) {
	$routeProvider
		.when('/help', {
			templateUrl : 'public/app/templates/site/page/help.html',
			controller  : 'pageController'
		})
		.when('/terms', {
			templateUrl : 'public/app/templates/site/page/terms.html',
			controller  : 'pageController'
		})
		.when('/privacy', {
			templateUrl : 'public/app/templates/site/page/privacy.html',
			controller  : 'pageController'
		})
		.when('/copyright', {
			templateUrl : 'public/app/templates/site/page/copyrights.html',
			controller  : 'pageController'
		})
		.when('/faq', {
			templateUrl : 'public/app/templates/site/page/faq.html',
			controller  : 'pageController'
		})
});

