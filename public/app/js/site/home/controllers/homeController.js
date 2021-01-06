var homePageApp = angular.module('homePageModule');

homePageApp.controller('homeController', ['$scope', 'Auth', '$rootScope', '$location', '$cookieStore', 'config', '$http',
    function ($scope, Auth, $rootScope, $location, $cookieStore, config, $http) {
        $rootScope.$on('$routeChangeStart', function (event, next) {
            var getBase = $location.path();
            var split_str = getBase.split("/");
            var subscribe = $cookieStore.get('subscribe');
            //console.log(subscribe);
            var allowed_methods = ['login', 'forgotPassword', 'reset', 'activation', 'username', 'help', 'register', 'passwordReset', 'terms', 'verification', 'getAllEducationalQuotes', 'privacy', 'copyright', 'faq'];
            var allowed_with_login = ['profile', 'changepassword', 'login', 'forgotPassword', 'reset', 'activation', 'username', 'help', 'register', 'passwordReset', 'terms', 'verification', 'getAllEducationalQuotes', 'paymentlist', 'privacy', 'copyright', 'faq', 'payment'];

            if (!Auth.isLoggedInUser()) {
                if (allowed_methods.indexOf(split_str[1]) > -1) {
                    return false;
                } else {
                    $location.url('/');
                }
            }

            //console.log(allowed_methods.indexOf(split_str[1]));
            if (subscribe == 0 && allowed_with_login.indexOf(split_str[1]) < 0 && getBase != "/") {
                //console.log('here');
                $location.path('/payment');
            }
        });

        $scope.getAllEducationalQuotes = function () {
            var serviceBase = config.url;
            $http.post(serviceBase + 'educationalquotes/getAllEducationalQuotes').success(function (response) {
                if (response.STATUS == 1) {
                    $scope.educationalquotes = response.DATA;
                    //$scope.grade.selected = $scope.grades[0];
                }
            });
        };
    }
]);
