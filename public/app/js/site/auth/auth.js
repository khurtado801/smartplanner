var homePageApp = angular.module('authModule', ['ngRoute','ngCookies']);

homePageApp.config(['$locationProvider','$routeProvider', 
   function($locationProvider, $routeProvider) {
      $routeProvider
         // route for the login page
         .when('/login', {
            templateUrl : 'public/app/templates/site/auth/login.html',
         	controller  : 'authController'
         })
         // route for the forgot password
         .when('/forgotPassword', {
            templateUrl : 'public/app/templates/site/auth/forgotPassword.html',
            // controller  : 'authController'
         })
         // route for the Reset password
         .when('/reset/', {
            templateUrl : 'public/app/templates/site/auth/reset.html',
         	// controller  : 'authController'
         })
        
         
}]);
