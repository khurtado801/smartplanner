// var loginPageApp = angular.module('loginPageModule', ['ngRoute','ngMaterial', 'ngMessages']);
var loginPageApp = angular.module('loginPageModule', ['ngRoute']);
loginPageApp.config(function ($routeProvider) {
    $routeProvider
        .when('/login', {
            templateUrl: 'public/app/templates/site/auth/login.html',
            controller: 'loginController'
        })
        .when('/forgotPassword', {
            templateUrl: 'public/app/templates/site/auth/forgotPassword.html',
            controller: 'loginController'

        })
        .when('/passwordReset/:id/:id/:vcode/:vcode', {
            templateUrl: 'public/app/templates/site/auth/passwordReset.html',
            controller: 'loginController'

        })
    //            .when('/changepassword', {
    //                templateUrl: 'public/app/templates/site/auth/changepassword.html',
    //                controller: 'loginController'
    //            })
    //            .when('/myprofile', {
    //                templateUrl: 'public/app/templates/site/auth/myprofile.html',
    //                controller: 'loginController'
    //            })
});
