var landingPageApp = angular.module('landingPageModule', ['ngRoute']);
landingPageApp.config(function($routeProvider) {
	$routeProvider
		.when('/landing', {
			templateUrl : 'public/app/templates/site/landing/landing.html',
			controller  : 'landingController'
		})
		.when('/verification/:email_verify_code/:id', { 
			//url: '/verification/:type',
	        templateUrl : 'public/app/templates/site/auth/login.html',
	        "resolve": {
	          "data": [
	            '$http','$routeParams','$route','config','Flash',
	            function($http,$routeParams,$route,config,Flash)
	            {  
	              return $http
	                .get(config.url+'user/verification/' + $route.current.params.email_verify_code + '/' + $route.current.params.id)
	                .then(
	                  function success(response) {  Flash.create('success', response.data.MESSAGE); return true; },
	                  function error(reason)     {  Flash.create('danger', reason.data.MESSAGE); return false; }
	                );
	            }
	          ]
	        }
	    })	
			
});

