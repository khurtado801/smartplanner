var homePageApp = angular.module('userPageModule');
homePageApp.controller('userController', ['User', '$cookieStore', '$scope', '$location', '$http', 'Flash', 'config', '$timeout','Auth', 'localStorageService',
    function (User, $cookieStore, $scope, $location, $http, Flash, config, $timeout,Auth,localStorageService) {

        $scope.loading = false;
        $scope.prodObj = [];

        $scope.userAuthcheck = function () {
            $http.get('api/user/profile').success(function (response) {
                if (response.length > 0)
                {
                    $scope.LoginuserData = response;
                    $localStorage.prodData = $scope.newArrivalsProductlist;
                } else
                {
                    $scope.LoginuserData = "notFound";
                }

                $scope.loading = false;
                $scope.productCategory = 'Others';

            }).error(function (response) {

                $scope.LoginuserData = "notFound";
                $scope.loading = false;
                console.log("Some Problem in new arrivals product listing")
            });

        };

        $scope.storeDataIntoStorage = function (data, upc) {

            if (data)
            {
                $scope.prodObj.push(data);
                $localStorage.prodData = $scope.prodObj;
                var path = 'productdetail?dept=Others&upc=' + upc;
                $location.url(path);
            } else {
                return false;
            }
        };

        $scope.changePassword = function (form) {

            var u = $cookieStore.get('userLogin');
            u = angular.fromJson(u);

            var current_password = $scope.currentPassword;
            var new_password = $scope.newpassword;
            var confirm_password = $scope.confirmPassword;

            var changePasswordObj = {
                "user_id": u[0].id,
                "current_password": current_password,
                "new_password": new_password,
                "confirm_password": confirm_password
            };

            $http.post('api/user/changePassword', changePasswordObj).success(function (response)
            {
                if (response.STATUS == 1) {
                    Flash.create('success', response.MESSAGE);
                    //$location.path('/');
                    $scope.currentPassword = "";
                    $scope.newpassword = "";
                    $scope.confirmPassword = "";
                    $scope.changepasswordForm.$setUntouched()
                } else {                    
                    Flash.create('danger', response.MESSAGE);
                }

            }).error(function (response) {
                //$location.path('/changepassword');
                    
                    
            });
        };

        $scope.getMyProfile = function (form) {

            var userid = Auth.getUserId();

            var myProfileObj = {
                "user_id": userid
            };

            $http.post('api/user/myProfile', myProfileObj).success(function (response)
            {
                if (response.STATUS == 1) {
                    $scope.user_profile = response.RESULT;

                } else {
                    Flash.create('danger', response.MESSAGE);
                }

            }).error(function (response) {
                //$location.path('/changepassword');
            });
        };

        $scope.updateMyProfile = function (form) {

            var userid = Auth.getUserId();

            var firstname = $scope.user_profile.firstname;
            var lastname = $scope.user_profile.lastname;
            var email = $scope.user_profile.email;
            var country = $scope.user_profile.country;
            var phone_number = $scope.user_profile.phone_number;

            var updateProfileObj = {
                "user_id": userid,
                "firstname": firstname,
                "lastname": lastname,
                "email": email,
                "country": country,
                "phone_number": phone_number
            };

            $http.post('api/user/updateProfile', updateProfileObj).success(function (response)
            {
                if (response.STATUS == 1) {
                    Flash.create('success', response.MESSAGE);
                } else {
                    Flash.create('danger', response.MESSAGE);
                }

            }).error(function (response) {

                //$location.path('/changepassword');
            });
        };        

        $scope.checkForActivePayment = function () {

            var userid = Auth.getUserId();

            var paymentObj = {
                "user_id": userid
            };
            localStorageService.set('editMode', 'No');
            $http.post('api/user/getSubscribedUserData', paymentObj).success(function (response)
            {
                if (response.STATUS == 1) {
                    $cookieStore.put('subscribe','1');
                } else {
                    $location.path('/payment');
                    Flash.create('danger', response.MESSAGE);
                }

            }).error(function (response) {
                //$location.path('/changepassword');
            });
        };
    }
]);


