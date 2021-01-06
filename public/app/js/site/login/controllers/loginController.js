var loginPageApp = angular.module('loginPageModule');

loginPageApp.controller('loginController', ['$rootScope','$scope','$uibModal','Flash', '$route', 'Auth', 'config', '$scope', '$location', '$http', '$routeParams','$cookieStore','localStorageService',
    function ($rootScope,$scope,$uibModal,Flash, $route, Auth, config, $scope, $location, $http, $routeParams,$cookieStore,localStorageService) {
        $scope.loading = true;
        var url = config.url;
        $scope.loading = false;
        $scope.login = function (form) {
            //console.log($scope);
            $scope.submitted = true;

            Auth.login({
                email: $scope.email,
                password: $scope.password
            })
                    .then(function (response) {

                        currentUserData = true;
                        // Logged in, redirect to home
                        if (response.STATUS == 1) {
                            $scope.authenticatedUser = response;
                            Auth.userlogin();
                            sessionStorage.userdata = response.RESULT;

                            Auth.setUser(response.RESULT);
                            console.log('SETUSER RES: ', response.RESULT[0]);
                            console.log("userType: ", response.RESULT[0].usertype);
                            if (response.RESULT[0].usertype == "Teacher")
                            {
                                Auth.isSubscribe(response.RESULT[0].id).then(function (res) {
                                    console.log("RES RES ID 0 ID: ", response.RESULT[0].id)
                                    console.log("RES STATUS: ", res.STATUS)
                                    if (res.STATUS == 1) {
                                        $location.path('/lession/create');
                                        console.log("IF RES STATUS IS 1 BODY:")
                                    }
                                });
                                //$location.path('/payment');
                                console.log("ELSE SUBSCRIBED: ")
                            }

                            Flash.create('success', response.MESSAGE);
                            Flash.clear();
                        } else {
                            Flash.create('danger', response.MESSAGE);
                            //$scope.getMessage = {"message": response.message, "type": "Error"};
                        }
                    })
                    .catch(function (err) {
                        console.log("CATCH");
                        $scope.errors.other = err.message;
                    });
        };
        $scope.checkPopup = function () {
            if(localStorageService.get("email_verify")=='1'){
                localStorageService.set("email_verify", '0');
                $scope.openPopup();
            }
        };
        $scope.modalInstance;
        $scope.popupClose = function () {
            $scope.modalInstance.close();   
        };
        
        $scope.openPopup = function (size, parentSelector) {
            var parentElem = parentSelector ? 
              angular.element($document[0].querySelector('.modal-demo ' + parentSelector)) : undefined;
            $rootScope.modalInstance = $uibModal.open({
              animation: $scope.animationsEnabled,
              ariaLabelledBy: 'modal-title',
              ariaDescribedBy: 'modal-body',
              templateUrl: 'myModalContent.html',
              size: size,
              appendTo: parentElem,
            });
          };

        $scope.forgotPassword = function (forgot) {

            if ($scope.email && $scope.email != "" && $scope.email != "undefined")
            {
                var forgotObj = {'email': $scope.email};

                $http.post('api/user/forgotPassword', forgotObj).success(function (response) {

                    if (response.STATUS == 1)
                    {
                        Flash.create('success', response.MESSAGE);
                        //$location.path('/passwordReset/:id/'+response.RESULT);
                    } else
                    {
                        //Flash.create('danger', response.MESSAGE);
                        //$location.path('/forgotPassword');
                        Flash.create('danger', response.MESSAGE);
                    }

                }).error(function (response) {
                    //$location.path('/forgotPassword');
                    Flash.create('danger', response.MESSAGE);
                });

            } else
            {
                $location.path('/forgotPassword');
                Flash.create('danger', "Invalid email address.");
            }

        };

        $scope.passwordReset = function (forgot) {

            var userId = $routeParams.id;
            var userVcode = $routeParams.vcode;

            if (userId && userId != "" && userId != "undefined")
            {
                var resetObj = {
                    //'password_verify_code' : forgot.password_verify_code,
                    'password_verify_code': userVcode,
                    'user_id': userId,
                    'password': forgot.password,
                    'confirm_password': forgot.confirm_password,
                };

                $http.post('api/user/passwordReset', resetObj).success(function (response)
                {
                    if (response.STATUS == 1)
                    {
                        $location.path('/login');
                        Flash.create('success', response.MESSAGE);
                    } else
                    {
                        //$location.path('/passwordReset/id/'+userId);
                        Flash.create('danger', response.MESSAGE);
                    }

                }).error(function (response) {

                    $location.path('/forgotPassword');
                });
            } else
            {
                $location.path('/forgotPassword');
            }

        };

    }
]).directive("matcher", function ($timeout) {

    return {
        restrict: "A",
        require: "ngModel",
        link: function (scope, element, attributes, ngModel) {

            ngModel.$validators.matcher = function (modelValue) {
                return attributes.matcher === modelValue;
            };
        }
    };
});
