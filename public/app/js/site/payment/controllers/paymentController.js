var paymentApp = angular.module("paymentModule");
paymentApp.controller("paymentController", [
    "$scope",
    "$http",
    "config",
    "Flash",
    "$cookieStore",
    "Auth",
    "$location",
    "localStorageService",
    "cfpLoadingBar",
    function (
        $scope,
        $http,
        config,
        Flash,
        $cookieStore,
        Auth,
        $location,
        localStorageService,
        cfpLoadingBar
    ) {
        // Auth.getPaymentStatus();
        $scope.payment = function (form) {
            console.log("FORM: ", form);

            var user_id = Auth.getUserId();
            var tempUser = Auth.isLoggedIn();
            console.log("data: ", tempUser);

            if ($scope.card_holder_name === undefined) {
                card_holder_name = "";
            } else {
                card_holder_name = $scope.card_holder_name;
            }

            if ($scope.card_number === undefined) {
                card_number = "";
            } else {
                card_number = $scope.card_number;
            }

            if (
                $scope.expiry_month === undefined ||
                $scope.expiry_month.selected === undefined
            ) {
                expiry_month = "";
            } else {
                expiry_month = $scope.expiry_month.selected.expiry_month;
            }

            if (
                $scope.expiry_year === undefined ||
                $scope.expiry_year.selected === undefined
            ) {
                expiry_year = "";
            } else {
                expiry_year = $scope.expiry_year.selected.expiry_year;
            }

            if ($scope.cvv === undefined) {
                cvv = "";
            } else {
                cvv = $scope.cvv;
            }

            var paymentObj = {
                user_id: user_id,
                //'plan_id': '1',
                plan_id: $scope.plan_id,
                plan_key: $scope.plan_key,
                card_holder_name: $scope.card_holder_name,
                card_number: card_number,
                expiry_month: $scope.expiry_month,
                expiry_year: $scope.expiry_year,
                cvv: $scope.cvv
            };

            console.log('OBJ: ', paymentObj);

            $http.post('api/store', paymentObj).success(function (response) {
                //console.log("here");
                if (response.STATUS == "1") {
                    $cookieStore.put('subscribe', '1');
                    Flash.create('success', response.MESSAGE);
                    $location.path('/lession/create');
                } else {
                    //console.log("error");
                    Flash.create('danger', response.MESSAGE);
                }
            }).error(function (response) {
                //console.log("else");
                if (response.MESSAGE == "") {
                    response.MESSAGE = "Something went wrong!";
                }
                Flash.create('danger', response.MESSAGE);
            });

        };

        $scope.stripeCharge = function () {

        };

        $scope.plan_change = function () {
            console.log('$scope.plan_list: ', $scope.plan_list);
            angular.forEach($scope.plan_list, function (value, key) {
                if (value.id == $scope.selected_plan) {
                    $scope.plan_key = value.id;
                    $scope.plan_name = value.nickname;
                    $scope.duration = value.interval;
                    $scope.duration_count = value.interval_count;
                    $scope.price = (value.amount) / 100;
                }
            });
            $scope.plan_id = $scope.selected_plan;
        };

        $scope.getPlanDetails = function () {
            var userData = JSON.parse(Auth.isLoggedInUser())[0].email;
            var user = Auth.g
            var user_id = Auth.getUserId();

            var serviceBase = config.url;

            $http
                .post("api/getPlanDetails")
                .success(function (response) {
                    if (response.STATUS == 1) {
                        var plan_list = response.DATA.data;
                        console.log('plan_list: ', plan_list);
                        var result = response.DATA;
                        console.log('RES: ', result);
                        console.log('RES: ', result.data[0].id);
                        $scope.plan_list = result.data;
                        console.log('Plan List: ', $scope.plan_list)
                        $scope.selected_plan = result.nickname;
                        $scope.plan_id = result.id;
                        $scope.plan_key = result.plan_key;
                        $scope.plan_name = result.nickname;
                        $scope.duration = result.interval;
                        $scope.duration_count = result.interval_count;
                        $scope.subscribe_date = result.subscribe_date;
                        $scope.expire_date = result.expire_date;
                        $scope.price = result.amount;
                        Flash.create("success", response.MESSAGE, result);
                        console.log('$scope.plan_name: ', $scope.plan_name)
                        //$location.url('/payment');

                        plan_list.forEach((plan) => {
                            console.log('Plan forEach', plan.nickname);
                           $scope.plan_name = plan.nickname;
                            console.log('Scope Plan: ', $scope.plan_name);
                        })
                    } else {
                        Flash.create("danger", response.MESSAGE);
                        $location.path("/");
                    }
                })
                .error(function (response) {
                    Flash.create("danger", response.MESSAGE);
                });
        };

        $scope.getStripeDetails = function (form) {

        }

        $scope.getAllPlans = function (form) {
            console.log("GET ALL PLANS:");
            //$scope.plans = [];
            var serviceBase = config.url;
            //var allplans = [];
            $http
                .post("api/getAllPlans")
                .success(function (response) {
                    if (response.STATUS == 1) {
                        var result = response.DATA;
                        $scope.plans = result;
                        //$scope.plan.selected = $scope.plans[0];
                        console.log('Results: ', result)
                    }
                });
        };

        $scope.list_payments = function () {
            console.log("LIST PAYMENTS:");
            var user_id = Auth.getUserId();
            var Obj = {
                user_id: user_id
            };
            var serviceBase = config.url;
            $http
                .post(serviceBase + "users/getMyAllPayments", Obj)
                .success(function (response) {
                    if (response.STATUS == 1) {
                        console.log(response.RESULT["my_payments"]);
                        $scope.payments = response.RESULT["my_payments"];
                        $scope.curr_status = response.RESULT["my_payments"]["status"];
                    } else {
                        $scope.payments = "";
                    }
                })
                .error(function (response) {
                    $scope.payments = "";
                });
        };

        $scope.filterValue = function ($event) {
            if (isNaN(String.fromCharCode($event.keyCode))) {
                $event.preventDefault();
            }
        };

        $scope.changeSubscriptionStatus = function (val, trans_id) {
            console.log("changeSubscriptionStatus:");
            var Obj = {
                transaction_id: trans_id,
                status: val
            };
            var serviceBase = config.url;
            $http
                .post(serviceBase + "paypalpayment/changeRecurringStatus", Obj)
                .success(function (response) {
                    if (response.STATUS == 1) {
                        console.log("TEST");
                        if (val == "Suspend" || val == "Cancel") {
                            $cookieStore.put("subscribe", "0");
                        } else {
                            $cookieStore.put("subscribe", "1");
                        }
                        Flash.create("success", response.MESSAGE);
                    } else {
                        Flash.create("danger", response.MESSAGE);
                    }
                })
                .error(function (response) {
                    $scope.curr_status = "";
                });
        };

        $scope.charge = function () {
            console.log('CHARGE!')
        }


    }
]);
