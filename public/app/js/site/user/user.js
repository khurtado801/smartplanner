var homePageApp = angular.module('userPageModule', ['ngRoute']);
homePageApp.config(function ($routeProvider) {
    $routeProvider            
            .when('/userdetail', {
                templateUrl: 'public/app/templates/site/product/userdetail.html',
                controller: 'userController'
            })
            .when('/changepassword', {
                templateUrl: 'public/app/templates/site/auth/changepassword.html',
                controller: 'userController'
            })
            .when('/profile', {
                templateUrl: 'public/app/templates/site/auth/myprofile.html',
                controller: 'userController'
            })
});

