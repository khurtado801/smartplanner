var homePageApp = angular.module('homePageModule');

homePageApp.controller('homeController', ['$scope', 'Auth', '$rootScope', '$location',
    function ($scope, Auth, $rootScope, $location) {
        $rootScope.$on('$routeChangeStart', function (event, next) {
            var getBase = $location.path();
            var split_str = getBase.split("/");

            if (!Auth.isLoggedInUser()) {
                console.log('not login');
                if (split_str[1] == "login" || split_str[1] == "forgotPassword" || split_str[1] == "reset"
                        || split_str[1] == "activation" || split_str[1] == "username" || split_str[1] == "register" || split_str[1] == "passwordReset") {
                    return false
                } else {
                    $location.url('/');
                }
            }
        });

        $scope.menuItems = ['modification', 'outline'];
        var getBase = $location.path();
        var split_str = getBase.split("/");
        if (split_str[1] == "outline"){
            $scope.activeMenu = $scope.menuItems[1];
        }else{
            $scope.activeMenu = $scope.menuItems[0];            
        }

        $scope.setActive = function (menuItem) {
            $scope.activeMenu = menuItem;
        }

    }
]);
