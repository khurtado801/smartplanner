var lessionModule = angular.module( "lessionModule" );

lessionModule.controller( "lessionController", [
    "$rootScope",
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
        $rootScope,
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
        /**TODO:  Check payment status if payment status is approved then user can create lesson*/
        //$scope.getPaymentStatus = function () {
        //};


        $scope.newLesson = function () {
            localStorageService.set( "editMode", "No" );
            $cookieStore.remove( "last_lession_id" );
            var payment_status = localStorageService.get( "payment_status" );
            localStorageService.clearAll();
            localStorageService.set( "payment_status", payment_status );

            $location.url( "/lession/create" );
        };

        $scope.shSkip = function () {
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            if ( last_lession_id === undefined ) {
                return false;
            } else {
                return true;
            }
        };

        $scope.getAlldata = function ( form ) {
            $scope.grades = [];
            $scope.grade = [];
            $scope.subjects = [];
            $scope.subject = [];
            $scope.themes = [];
            $scope.theme = [];
            var serviceBase = config.url;
            var allgrade = [];
            $http
                .post( serviceBase + "grade/getAllGrades" )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.grades = response.DATA;
                    }
                } );
        };

        $scope.getSubjects = function ( form ) {
            var editMode = localStorageService.get( "editMode" );
            var checkboxes = [];
            if ( editMode == "Yes" ) {
                localStorageService.set( "Learning_target", checkboxes );
                localStorageService.set( "key_concepts", checkboxes );
            }

            $scope.grade.selected.id = $scope.grade.selected.id || "";
            if ( $scope.grade.selected.id == "" ) {
                $scope.grade = [];
            } else {
                var Obj = {
                    grade_id: $scope.grade.selected.id
                };
                var serviceBase = config.url;

                $scope.themes = [];
                $scope.theme = [];
                $scope.subjects = [];
                $scope.subject = [];

                $http
                    .post( serviceBase + "subjects/getSubjectsByGrade", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            $scope.subjects = response.DATA;
                        } else {
                            $scope.subjects = "";
                        }
                    } )
                    .error( function ( response ) {
                        $scope.subjects = "";
                    } );
            }
        };

        $scope.changeThemes = function ( form ) {
            var editMode = localStorageService.get( "editMode" );
            var checkboxes = [];
            if ( editMode == "Yes" ) {
                localStorageService.set( "Learning_target", checkboxes );
            }
            $scope.theme.selected.theme_id = $scope.theme.selected.theme_id || "";
            if ( $scope.theme.selected.theme_id == "" ) {
                $scope.theme = [];
            }
        };

        $scope.getThemes = function ( form ) {
            var editMode = localStorageService.get( "editMode" );
            var checkboxes = [];
            if ( editMode == "Yes" ) {
                localStorageService.set( "Learning_target", checkboxes );
            }
            $scope.subject.selected.id = $scope.subject.selected.id || "";
            if ( $scope.subject.selected.id == "" ) {
                $scope.subject = [];
            } else {
                var Obj = {
                    grade_id: $scope.grade.selected.id,
                    subject_id: $scope.subject.selected.id
                };
                $scope.themes = [];
                // $scope.theme = [];
                var serviceBase = config.url;
                $http
                    .post( serviceBase + "theme/getThemesByGradeAndSubjects", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            $scope.themes = response.DATA;
                        } else {
                            $scope.themes = "";
                        }
                    } )
                    .error( function ( response ) {
                        $scope.themes = "";
                    } );
            }
        };

        $scope.createlession = function ( form ) {
            var user_id = Auth.getUserId();

            var paymentObj = {
                user_id: user_id
            };

            $http
                .post( "api/user/getSubscribedUserData", paymentObj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        var grade_id = "";
                        var subject_id = "";
                        var theme_id = "";

                        if ( $scope.grade.selected === undefined ) {
                            grade_id = "";
                        } else {
                            grade_id = $scope.grade.selected.id;
                        }

                        if (
                            $scope.subject === undefined ||
                            $scope.subject.selected === undefined
                        ) {
                            subject_id = "";
                        } else {
                            subject_id = $scope.subject.selected.id;
                        }

                        if (
                            $scope.theme === undefined ||
                            $scope.theme.selected === undefined
                        ) {
                            theme_id = "";
                        } else {
                            theme_id = $scope.theme.selected.theme_id;
                        }

                        var user_id = Auth.getUserId();

                        var lession_id = $cookieStore.get( "last_lession_id" );

                        var Obj = {
                            grade_id: grade_id,
                            subject_id: subject_id,
                            theme_id: theme_id,
                            unit_title: $scope.unit_title,
                            user_id: user_id
                        };
                        if ( lession_id ) {
                            var Obj = {
                                grade_id: grade_id,
                                subject_id: subject_id,
                                theme_id: theme_id,
                                unit_title: $scope.unit_title,
                                user_id: user_id,
                                id: lession_id[ "last_id" ]
                            };
                        }

                        var serviceBase = config.url;
                        $http
                            .post( serviceBase + "lesson/create", Obj )
                            .success( function ( response ) {
                                if ( response.STATUS == 1 ) {
                                    if ( !lession_id ) {
                                        $cookieStore.put( "last_lession_id", response.DATA );
                                    }
                                    $location.url( "/outline/create" );
                                } else {
                                    Flash.create( "danger", response.MESSAGE );
                                }
                            } )
                            .error( function ( response ) {} );
                    } else {
                        $location.path( "/payment" );
                    }
                } )
                .error( function ( response ) {
                    $location.path( "/payment" );
                } );
        };

        $scope.list_lessons = function ( form ) {
            var user_id = Auth.getUserId();
            var Obj = {
                user_id: user_id
            };

            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/getMyAllLessons", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.lessons = response.DATA;
                    } else {
                        $scope.lessons = "";
                    }
                } )
                .error( function ( response ) {
                    $scope.lessons = "";
                } );
        };

        $scope.edit = function ( id ) {
            localStorageService.set( "editMode", "Yes" );
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var newlesson = {
                last_id: id
            };
            $cookieStore.put( "last_lession_id", newlesson );

            var user_id = Auth.getUserId();
            var Obj = {
                lesson_id: id
            };
            var serviceBase = config.url;
            $http
                .post( serviceBase + "LessonUser/getUserlesson", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        var result = response.DATA;
                        keyconcept_array = learningtarget_array = [];
                        if ( result.user_lessons.keyconcept_id != null ) {
                            var keyconceptarr = result.user_lessons.keyconcept_id.toString();
                            var keyconcept_array = keyconceptarr.split( "," );
                        }
                        if ( result.user_lessons.learningtarget_id != null ) {
                            var lear = result.user_lessons.learningtarget_id.toString();
                            var learningtarget_array = lear.split( "," );
                        }
                        localStorageService.set( "key_concepts", keyconcept_array );
                        localStorageService.set( "Learning_target", learningtarget_array );

                        if ( learningtarget_array.length > 0 ) {
                            $scope.disabletab = false;
                        }
                    } else {
                        var keyconcept_array = [];
                        var learningtarget_array = [];
                        localStorageService.set( "key_concepts", keyconcept_array );
                        localStorageService.set( "Learning_target", learningtarget_array );
                    }
                } )
                .error( function ( response ) {} );

            $location.path( "/lession/create" );
        };

        $scope.delete = function ( id ) {
            var conf_del = confirm(
                "Are you sure you want to permanently delete this record?"
            );
            if ( conf_del == true ) {
                var user_id = Auth.getUserId();
                var Obj = {
                    lesson_id: id
                };
                var serviceBase = config.url;
                $http
                    .post( serviceBase + "LessonUser/deleteUserlesson", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            var result = response.DATA;
                            var index = $scope.lessons
                                .map( function ( e ) {
                                    return e.id;
                                } )
                                .indexOf( id );
                            $scope.lessons.splice( index, 1 );

                            $location.path( "/lession/list_lessons" );
                        } else {}
                    } )
                    .error( function ( response ) {} );
                $location.path( "/lession/list_lessons" );
            }
        };

        $scope.getEditData = function () {
            var id = $cookieStore.get( "last_lession_id" );

            if ( id !== undefined ) {
                var Obj = {
                    lesson_id: id[ "last_id" ]
                };
                var serviceBase = config.url;

                $http
                    .post( serviceBase + "lesson/getLessonsUserData", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            var result = response.DATA;
                            $scope.grade.selected = {
                                name: result.grade_name,
                                id: result.grade_id
                            };
                            $scope.unit_title = result.unit_title;
                            $scope.subjects = result.subject_list;
                            $scope.subject.selected = {
                                name: result.subject_name,
                                id: result.subject_id
                            };
                            $scope.themes = result.theme_list;
                            $scope.theme.selected = {
                                name: result.theme_name,
                                theme_id: result.theme_id
                            };
                            $location.path( "/lession/create" );
                        }
                    } )
                    .error( function ( response ) {
                        $location.path( "/lession/list_lessons" );
                    } );
            }
        };
    }
] );

lessionModule.directive( "uiSelectRequired", function () {
    return {
        require: "ngModel",
        link: function ( scope, elm, attrs, ctrl ) {
            ctrl.$validators.uiSelectRequired = function ( modelValue, viewValue ) {
                return modelValue && modelValue.length;
            };
        }
    };
} );