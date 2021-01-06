var pageApp = angular.module('pageModule');
	pageApp.controller('pageController', ['$scope','$http', 
            function($scope, $http) {
                        $scope.help = function () {

                            var helpObj = {'slug' : 'help'};
                            //alert("zzzz");
                            $http.post('api/cms/getAllCms', helpObj).success(function (response) {
                                if (response.STATUS == 1)
                                {
                                    $scope.helpDetail = response.DATA;
                                } else
                                {
                                    $scope.helpDetail = "";
                                }

                            }).error(function (response) {

                                $scope.helpDetail = "";
                            });
                        };
                        
                        $scope.terms = function () {

                            var termsObj = {'slug' : 'terms_and_conditions'};
                            //alert("zzzz");
                            $http.post('api/cms/getAllCms', termsObj).success(function (response) {
                                if (response.STATUS == 1)
                                {
                                    $scope.termDetail = response.DATA;
                                } else
                                {
                                    $scope.termDetail = "";
                                }

                            }).error(function (response) {

                                $scope.termDetail = "";
                            });
                        };
                        
                        $scope.privacy = function () {

                            var privacyObj = {'slug' : 'privacy_policy'};
                            //alert("zzzz");
                            $http.post('api/cms/getAllCms', privacyObj).success(function (response) {
                                //console.log(response);
                                if (response.STATUS == 1)
                                {
                                    $scope.privacyDetail = response.DATA;
                                } else
                                {
                                    $scope.privacyDetail = "";
                                }

                            }).error(function (response) {

                                $scope.privacyDetail = "";
                            });
                        };
                        
                        $scope.copyrights = function () {

                            var copyrightsObj = {'slug' : 'copy_rights'};
                            //alert("zzzz");
                            $http.post('api/cms/getAllCms', copyrightsObj).success(function (response) {
                                console.log(response);
                                if (response.STATUS == 1)
                                {
                                    $scope.copyrightsDetail = response.DATA;
                                } else
                                {
                                    $scope.copyrightsDetail = "";
                                }

                            }).error(function (response) {

                                $scope.copyrightsDetail = "";
                            });
                        };
                        
                        $scope.faq = function () {

                            var faqObj = {'slug' : 'faq'};
                            //alert("zzzz");
                            $http.post('api/cms/getAllCms', faqObj).success(function (response) {
                                console.log(response);
                                if (response.STATUS == 1)
                                {
                                    $scope.faqDetail = response.DATA;
                                } else
                                {
                                    $scope.faqDetail = "";
                                }

                            }).error(function (response) {

                                $scope.faqDetail = "";
                            });
                        };
            }
	]);
