var landingPageApp = angular.module('landingPageModule');

landingPageApp.controller('landingController', ['$scope', 'Auth', '$rootScope', '$location', '$cookieStore', 'config', '$http',
    function ($scope, Auth, $rootScope, $location, $cookieStore, config, $http) {
        $rootScope.$on('$routeChangeStart', function (event, next) {
            var getBase = $location.path();
            var split_str = getBase.split("/");
            var subscribe = $cookieStore.get('subscribe');
            var allowed_methods = ['login', 'register', 'terms', 'getAllEducationalQuotes', 'privacy', 'copyright', 'faq'];
            var allowed_with_login = ['login', 'help', 'register', 'terms', 'getAllEducationalQuotes', 'privacy', 'copyright'];

            if (!Auth.isLoggedInUser()) {
                if (allowed_methods.indexOf(split_str[1]) > -1) {
                    return false;
                } else {
                    $location.url('/login');
                }
            }

            if (subscribe == 0 && allowed_with_login.indexOf(split_str[1]) < 0 && getBase != "/") {
                $location.path('/payment');
            }
        });
    }
]);
