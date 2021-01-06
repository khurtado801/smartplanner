'use strict';

angular.module('truckForLoadSite')
    .factory('Auth', function Auth(config, $location, $rootScope, $http, User, $cookieStore, $q, $window, Flash, localStorageService) {
        var currentUser = {};
        var currentUserData = {};
        var formToken = "";
        var serviceBase = config.url;
        var is_currentUser = "";
        var user;

        if ($cookieStore.get('tokenId')) {
            currentUser = User.getByToken();
            console.log("current: ", currentUser)
        }


        return {

            setUser: function (aUser) {
                user = angular.toJson(aUser);
                $cookieStore.put('userLogin', user);
            },
            isLoggedIn: function () {
                var user = $cookieStore.get('userLogin');
                if (user) {
                    console.log('login che');
                    return true;
                } else {
                    //  console.log('login nathi');
                    return false;
                }
                //return(user)? user : false;
            },
            removeUserCoockie: function () {
                $cookieStore.remove("userLogin");
            },
            getUserData: function () {
                var u = $cookieStore.get('userLogin');
                return u.RESULT;
            },
            /*setUser : function(user){
              user = angular.toJson(user);  
              user = $cookieStore.get('userLogin'); 
            },*/


            isLoggedInUser: function () {
                if ($cookieStore.get('userLogin') != "") {
                    user = $cookieStore.get('userLogin');
                    return (user) ? user : false;
                } else {
                    return false;
                }

            },
            userlogin: function (user, callback) {
                if ($cookieStore.get('tokenId')) {
                    return true;
                } else {
                    return false;
                }
            },

            /**
             * Authenticate user and save token
             *
             * @param  {Object}   user     - login info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            login: function (user, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                $http.post(serviceBase + 'user/login', {
                    email: user.email,
                    password: user.password,
                    _token: user._token
                }).
                success(function (data) {

                    if (data.STATUS == 1) {
                        $cookieStore.put('userLogin', data.RESULT[0]);
                        user = $cookieStore.put('tokenId', data.RESULT[0].tokenId);
                        // currentUser = User.getByToken();
                        currentUser = user;
                        deferred.resolve(data);
                        return cb();
                    } else {
                        $cookieStore.remove('userLogin');
                        $cookieStore.remove('tokenId');
                        deferred.resolve(data);
                        return cb();
                    }

                }).
                error(function (err) {
                    this.logout();
                    deferred.reject(err);
                    return cb(err);
                }.bind(this));

                return deferred.promise;
            },




            /**
             * Forgot Password - Front side
             *
             * @param  {Object}   user     - login info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            forgot: function (user, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                $http.post(serviceBase + 'user/forgot', {
                    mobile: user.mobile,
                    _token: user._token
                }).
                success(function (data) {

                    deferred.resolve(data);
                    return cb();
                }).
                error(function (err) {
                    this.logout();
                    deferred.reject(err);
                    return cb(err);
                }.bind(this));

                return deferred.promise;
            },


            /**
             * Authenticate user and save token - Site
             *
             * @param  {Object}   user     - login info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            resetPassword: function (resetpassword, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                $http.post(serviceBase + 'user/resetpassword', {
                    password: resetpassword.password,
                    resettoken: resetpassword.resettoken,
                    _token: resetpassword._token
                }).
                success(function (data) {

                    deferred.resolve(data);
                    return cb();
                }).
                error(function (err) {
                    this.logout();
                    deferred.reject(err);
                    return cb(err);
                }.bind(this));

                return deferred.promise;
            },


            /**
             * Authenticate user and save token - Site
             *
             * @param  {Object}   user     - login info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            checkResetToken: function (token, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                $http.get(serviceBase + 'user/checkresettoken/' + token.resettoken).
                success(function (data) {
                    if (data.error == true) {
                        $location.url('/login');
                    }

                    deferred.resolve(data);
                    return cb();
                }).
                error(function (err) {
                    this.logout();
                    deferred.reject(err);
                    return cb(err);
                }.bind(this));

                return deferred.promise;
            },

            /**
             * Authenticate user and save token
             *
             * @param  {Object}   user     - login info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            register: function (user, callback) {
                var cb = callback || angular.noop;
                var deferred = $q.defer();

                $http.post(serviceBase + 'user/register', {
                    firstname: user.firstname,
                    lastname: user.lastname,
                    email: user.email,
                    password: user.password,
                    confirm_password: user.confirm_password,
                    country: user.country,
                    phone_number: user.phone_number,
                    usertype: user.usertype
                }).
                success(function (data) {

                    $cookieStore.put('tokenId', data.tokenId);
                    currentUser = User.getByToken();
                    currentUser = data;
                    deferred.resolve(data);
                    return cb();
                }).
                error(function (err) {
                    this.logout();
                    deferred.reject(err);
                    return cb(err);
                }.bind(this));

                return deferred.promise;
            },

            /**
             * Delete access token and user info
             *
             * @param  {Function}
             */
            logout: function () {
                $cookieStore.remove('userLogin');
                //$sessionStorage.remove(); 
                $cookieStore.remove('tokenId');
                currentUser = {};
                $location.path('/login');
            },

            /**
             * Create a new user
             *
             * @param  {Object}   user     - user info
             * @param  {Function} callback - optional
             * @return {Promise}
             */
            createUser: function (user, callback) {
                var cb = callback || angular.noop;

                return User.save(user,
                    function (data) {
                        $cookieStore.put('tokenId', data.token);
                        currentUser = User.get();
                        return cb(user);
                    },
                    function (err) {
                        this.logout();
                        return cb(err);
                    }.bind(this)).$promise;
            },

            /**
             * Change password
             *
             * @param  {String}   oldPassword
             * @param  {String}   newPassword
             * @param  {Function} callback    - optional
             * @return {Promise}
             */
            changePassword: function (oldPassword, newPassword, callback) {
                var cb = callback || angular.noop;

                return User.changePassword({
                    id: currentUser._id
                }, {
                    oldPassword: oldPassword,
                    newPassword: newPassword
                }, function (user) {
                    return cb(user);
                }, function (err) {
                    return cb(err);
                }).$promise;
            },

            /**
             * Gets all available info on authenticated user
             *
             * @return {Object} user
             */

            getCurrentUser: function () {
                currentUser = User.getByToken();
                // console.log(currentUser);
                return currentUser;
            },
            /**
             * Gets all available info on authenticated user
             *
             * @return {Object} user
             */
            getSessionCurrentUser: function () {
                currentUserData = $cookieStore.get('userLogin');
                return currentUserData;
            },

            /**
             * Gets all available info on authenticated user
             *
             * @return {Object} user
             */
            getFormToken: function () {
                formToken = User.getFormToken();
                return formToken;
            },

            /**
             * Check if a user is logged in
             *
             * @return {Boolean}
             */
            //      isLoggedIn: function() {
            //        return currentUser.hasOwnProperty('role');
            //      },

            /**
             * Waits for currentUser to resolve before checking if user is logged in
             */
            isLoggedInAsync: function (cb) {
                if (currentUser.hasOwnProperty('$promise')) {
                    currentUser.$promise.then(function () {
                        cb(true);
                    }).catch(function () {
                        cb(false);
                    });
                } else if (currentUser.hasOwnProperty('role')) {
                    cb(true);
                } else {
                    cb(false);
                }
            },

            /**
             * Check if a user is an admin
             *
             * @return {Boolean}
             */
            isAdmin: function () {
                return currentUser.role === '';
            },

            /**
             * get User id
             *
             * @return {id}
             */
            getUserId: function () {
                var u = $cookieStore.get('userLogin');
                u = angular.fromJson(u);
                var id = u[0].id;
                return id;
            },


            /**
             * Get auth token
             */
            getToken: function () {
                return $cookieStore.get('tokenId');
            },

            isSubscribe: function (id) {
                var deferred = $q.defer();
                var subscribeObj = {
                    'user_id': id
                };
                $http.post(serviceBase + 'user/getSubscribedUserData', subscribeObj).success(function (response) {
                    if (response.STATUS == 0) {
                        console.log("RES STATUS: ", response.STATUS)
                        deferred.resolve(response);
                        $cookieStore.put('subscribe', '0');
                        $location.path('/payment');
                        return false;
                    } else {
                        deferred.resolve(response);
                        $cookieStore.put('subscribe', '1');
                    }
                }).error(function (response) {
                    //$location.path('/help');
                });
                return deferred.promise;
            },

            /**
             * Get auth token
             */
            getPaymentStatus: function () {
                var userid = this.getUserId();
                // console.log(userid);
                var subscribeObj = {
                    'user_id': userid
                };
                var payment_status = localStorageService.get('payment_status');
                console.log(payment_status);
                if (payment_status != 1) {
                    $http.post(serviceBase + 'user/checkpayment', subscribeObj).success(function (response) {
                        if (response.STATUS == 0) {
                            $cookieStore.put('subscribe', '0');
                            Flash.create('danger', "Your payment is still not complete!");
                            $location.path('/payment');
                            // return false;
                        } else {

                            //deferred.resolve(response);
                            $cookieStore.put('subscribe', '1');
                            localStorageService.set('payment_status', '1');
                        }
                    }).error(function (response) {
                        //$location.path('/help');
                    });
                }
            },
        };
    });
