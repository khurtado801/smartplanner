var freelancerApp = angular.module('freelancerModule', ['ngRoute']);

freelancerApp.config(function($routeProvider) {
	$routeProvider
		.when('/freelancer/profile/update', {
			templateUrl : 'public/app/templates/site/freelancer/freelancerUpdate.html',
			controller  : 'freelancerController'
		})
		.when('/freelancer/profile/dashboard', {
			templateUrl : 'public/app/templates/site/freelancer/freelancerDashboard.html',
			controller  : 'freelancerController'
		})
		.when('/freelancer/profile/payment', {
			templateUrl : 'public/app/templates/site/freelancer/freelancerPayment.html',
			controller  : 'freelancerController'
		})
		.when('/freelancer/profile/changePassword', {
			templateUrl : 'public/app/templates/site/freelancer/changePassword.html',
			controller  : 'freelancerController'
		})
		.when('/freelancer/profile/companyDetails', {
			templateUrl : 'public/app/templates/site/freelancer/companyDetails.html',
			controller  : 'freelancerController'
		})
		.when('/freelancer/project/view/id/:id', {
			templateUrl : 'public/app/templates/site/project/projectView.html',
			controller  : 'freelancerController'
		})
});

