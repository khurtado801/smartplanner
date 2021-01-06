var paymentApp = angular.module('paymentModule', ['ngRoute']);

paymentApp.config(function($routeProvider) {
	$routeProvider
		.when('/payment', {
			templateUrl : 'public/app/templates/site/payment/payment.html',
			controller  : 'paymentController'
		})
		.when('/paymentlist', {
			templateUrl : 'public/app/templates/site/payment/list_payment.html',
			controller  : 'paymentController'
		})
		.when('/invoice', {
			templateUrl : 'public/app/templates/site/payment/invoice.html',
			controller  : 'paymentController'
		})
});

