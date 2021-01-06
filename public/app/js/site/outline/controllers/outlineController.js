//var outlineModule = angular.module('myApps',['ui.tinymce']);
var outlineModule = angular.module( "outlinePageModule" );
outlineModule.controller( "outlineController", [
    "$scope",
    "$uibModal",
    "$http",
    "config",
    "Flash",
    "$cookieStore",
    "$window",
    "localStorageService",
    "$location",
    "$sce",
    "cfpLoadingBar",
    "$timeout",
    "Auth",
    function (
        $scope,
        $uibModal,
        $http,
        config,
        Flash,
        $cookieStore,
        $window,
        localStorageService,
        $location,
        $sce,
        cfpLoadingBar,
        $timeout,
        Auth
    ) {
        $scope.disblelink = "disable-link";
        $scope.classdiv = $scope.modi_re = false;
        $scope.emptyDone = "no";
        $scope.classBlueBk = "";
        $scope.classWhiteBk = "";
        $scope.allBloomsWebbs = [];
        $scope.selectedWebb = "hideWebb";
        $scope.IsVisible = false;
        $scope.webbListIdx = -1;
        $scope.activityListIdx = -1;
        $scope.deliveryListIdx = -1;
        $scope.standardListIdx = -1;
        $scope.modificationListIdx = -1;
        $scope.bloomRadioIdx = -1;
        $scope.activityRadioIdx = -1;
        $scope.deliveryRadioIdx = -1;
        $scope.standardRadioIdx = -1;
        $scope.modificationRadioIdx = -1;
        $scope.bloomId;
        $scope.webbId;
        $scope.activityId;
        $scope.deliveryId;
        $scope.standardId;
        $scope.modificationId;
        $scope.activities = {};

        let webbListNew = [ "No data found;" ];
        let webbsList = [];
        let activityList = [];
        let deliveryList = [];
        let standardList = [];
        let modificationList = [];
        let radioBloom = "";
        let radioActivity = "";
        let radioDelivery = "";
        let radioStandard = "";
        let radioModification = "";
        let prevBloomId = "";
        let curWebb = "";
        let curActivity = "";
        let prevWebbId = "";
        let currWebbId = "";
        let retrievedLessonSequence = {};
        let prevActivities = {};
        let sequence = [];
        let lessonSequence = {
            bloom_id: "",
            bloom_name: "",
            bloom_description: "",
            webb_id: "",
            webb_name: "",
            webb_description: "",
            activity_id: "",
            activity_name: "",
            activity_description: "",
            delivery_id: "",
            delivery_name: "",
            standard_id: "",
            standard_name: "",
            modification_id: "",
            modification_name: "",
        };
        let prevLessonSequence = {
            bloom_id: "",
            bloom_description: "",
            webb_id: "",
            webb_name: "",
            webb_description: "",
            activity_id: "",
            activity_name: "",
            activity_description: "",
            delivery_id: "",
            delivery_name: "",
            standard_id: "",
            standard_name: "",
            modification_id: "",
            modification_name: "",
        };

        // Get lesson sequence data
        $scope.getAllSequenceData = function ( form ) {
            // $scope.grades = [];
            // $scope.grade = [];
            // $scope.subjects = [];
            // $scope.subject = [];
            // $scope.themes = [];
            // $scope.theme = [];
            // var allgrade = [];
            $scope.tableFinished = "0";
            sequence = [];

            $scope.userCompletesData = "selSequencePartBackground";
            $scope.userSelectedHeader = "selSequencePartHeader";
            var serviceBase = config.url;

            // Get all bloom's
            $http
                .post( serviceBase + "bloom/getAllBlooms" )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.blooms = response.DATA;
                        $http
                            .post( serviceBase + "webb/getWebbByBloom", {
                                ignoreLoadingBar: true,
                            } )
                            .success( function ( response ) {
                                if ( response.STATUS == 1 ) {
                                    webbsList = angular.fromJson( response.DATA );
                                    webbsListNew = webbsList;
                                    $scope.webbsList = webbsList;
                                    $scope.allBloomsWebbs = webbsList;
                                } else {
                                    $scope.webbs = "";
                                }
                            } )
                            .error( function ( response ) {
                                $scope.webbs = "";
                            } );
                    }
                } );
        };

        $scope.getRadioValue = function (
            radioBloomId,
            radiobloom_description,
            radiobloom_name,
            bloomIdx
        ) {
            $scope.bloomWebbHeader = "";
            $scope.activityHeader = "";
            $scope.deliveryHeader = "";
            $scope.standardHeader = "";
            $scope.modificationsHeader = "";
            $scope.bloomWebbData = "";
            $scope.activityData = "";
            $scope.deliveryData = "";
            $scope.standardData = "";
            $scope.modificationData = "";
            $scope.activities = {};
            $scope.deliveries = {};
            $scope.standards = {};
            $scope.modifications = {};
            lessonSequence.bloom_id = 0;
            lessonSequence.bloom_description = 0;
            lessonSequence.bloom_name = 0;
            lessonSequence.webb_id = 0;
            lessonSequence.webb_description = 0;
            lessonSequence.activity_id = 0;
            lessonSequence.activity_name = 0;
            lessonSequence.activity_description = 0;
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;
            $scope.deliveryRadioIdx = 0;
            $scope.deliveryIdx = 0;
            $scope.standardRadioIdx = 0;
            $scope.standardRadioIdxHideShow = 0;
            $scope.standardIdx = 0;
            $scope.modificationRadioIdx = 0;
            $scope.modificationRadioIdxHideShow = 0;
            $scope.modificationIdx = 0;
            $scope.webbIdResult = 0;

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            let retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            $scope.webbListIdx = bloomIdx;
            $scope.bloomRadioIdx = bloomIdx;
            let inputs = document.getElementsByTagName( "inputs" );
            // below doesn't return anything, need value of
            radioBloom = document.getElementsByName( "bloomList" );
            $scope.bl = radioBloom[ radioBloomId - 1 ].value;

            // watch bloom  radio button value to match bloom
            $scope.$watch( "bl", function ( bl ) {
                angular.forEach( webbsList, function ( value, key ) {
                    if ( value.bloom_id == $scope.bl ) {
                        $scope.availableOpts = webbsList.filter(
                            ( w ) => w.bloom_id == value.bloom_id
                        );
                    }
                } );
            } );

            lessonSequence.bloom_id = radioBloomId;
            lessonSequence.bloom_id = radioBloomId;
            lessonSequence.bloom_description = radiobloom_description;
            lessonSequence.bloom_name = radiobloom_name;

            let temp_array = {
                // bloom: {
                //     id: radioBloomId,
                //     name: radiobloom_name,
                //     desc: radiobloom_description
                // }
                id: radioBloomId,
                definition: "bloom",
                name: radiobloom_name,
                desc: radiobloom_description,
            };
            sequence.push( temp_array );

            console.log( "sequence length: ", sequence.length );
            console.log( " sequence: ", sequence );
            console.log( "sequence length: ", sequence.length );

            // console.log("sequence: ", sequence[0].name)
            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage
            jsonLocalStorageString = localStorage.getItem( "localLessonSequence" );
            // parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse( jsonLocalStorageString );

            if (
                retrievedLessonSequence.webb_id ||
                retrievedLessonSequence.webb_id != 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";
            } else {
                $scope.bloomWebbHeader = "";
                $scope.bloomWebbData = "";
            }
        };

        $scope.selectedItemChanged = function ( selWebbId, selwebb_description ) {
            document.getElementById( "activitySequenceData" ).style.visibility =
                "visible";
            $scope.bloomWebbHeader = "";
            $scope.bloomWebbData = "";
            $scope.activityData = "";
            $scope.deliveryData = "";
            $scope.activities = {};
            $scope.deliveries = {};
            $scope.activityRadioIdx = 0;
            $scope.deliveryRadioIdx = 0;
            $scope.deliveryIdx = 0;
            $scope.webbIdResult = 0;
            lessonSequence.webb_id = 0;
            lessonSequence.webb_description = 0;
            lessonSequence.activity_id = 0;
            lessonSequence.activity_name = 0;
            lessonSequence.activity_description = 0;
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string to JS object
            let retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            currWebbId = selWebbId;

            // get activities by bloom_id and webb_id
            let Obj = {
                bloom_id: retrievedLessonSequence.bloom_id,
                webb_id: selWebbId,
            };

            let serviceBase = config.url;

            // POST request to activity endpoint, get activities by bloom and webb
            $http
                .post( serviceBase + "activity/getActivitiesByBloomAndWebb", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.activities = response.DATA;
                    }
                } )
                .error( function ( response ) {
                    $scope.activities;
                } );

            $scope.webbIdResult = selWebbId;
            lessonSequence.webb_id = selWebbId;
            lessonSequence.webb_description = selwebb_description;

            //     let temp_array = {
            //         webb: {
            //             id: selWebbId,
            //             desc: selwebb_description
            //         }
            //     }
            //    sequence.push(temp_array);
            //    console.log("sequence length: ", sequence.length)
            //    console.log("sequence: ", sequence)

            let temp_array = {
                id: selWebbId,
                definition: "webb",
                name: selwebb_description,
                desc: selwebb_description,
            };
            sequence.push( temp_array );

            console.log( "sequence length: ", sequence.length );
            console.log( " sequence: ", sequence );
            console.log( "sequence length: ", sequence.length );

            // console.log("sequence: ", sequence[0].name)
            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            //var webbsArr = localStorageService.get( "webbs" )
            if (
                retrievedLessonSequence.webb_id ||
                retrievedLessonSequence.webb_id != 0 ||
                retrievedLessonSequence.activity_id != 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";

                $scope.bloomName = retrievedLessonSequence.bloom_name;
                $scope.bloomDescription = retrievedLessonSequence.bloom_description;
                $scope.webbDescription = retrievedLessonSequence.webb_description;
                //$scope.hideBloomWebbs = 0;
            } else {
                $scope.bloomWebbHeader = "";
                $scope.bloomWebbData = "";
            }

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        $scope.getActivityRadioValue = function (
            radioActivityId,
            radioActivity_name,
            radioActivity_description,
            activityIdx
        ) {
            $scope.activityRadioIdxHideShow = activityIdx + 1;

            // set activity lesson sequence to 0, zeroed activity sequence
            lessonSequence.activity_id = 0;
            lessonSequence.activity_name = 0;
            lessonSequence.activity_description = 0;
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            $scope.deliveryData = "";
            $scope.deliveries = {};

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string to JS object
            let retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            $scope.activityListIdx = activityIdx;
            $scope.activityRadioIdx = activityIdx;

            // get selected activity radio input value
            let inputs = document.getElementsByTagName( "inputs" );
            radioActivity = document.getElementsByName( "activityList" );

            $scope.act = radioActivity[ activityIdx ].value;

            lessonSequence.activity_name;
            $scope.activityRadioIdx = activityIdx;
            $scope.activityHeader = "selSequencePartHeader";
            $scope.activityData = "selSequencePartBackground";

            lessonSequence.activity_id = radioActivityId;
            lessonSequence.activity_name = radioActivity_name;
            lessonSequence.activity_description = radioActivity_description;

            //     let temp_array = {
            //         activity: {
            //             id: radioActivityId,
            //             name: radioActivity_name,
            //             desc: radioActivity_description,}
            //     }
            //    sequence.push(temp_array);
            //    console.log("sequence length: ", sequence.length)
            //    console.log("sequence: ", sequence)

            let temp_array = {
                id: radioActivityId,
                definition: "activity",
                name: radioActivity_name,
                desc: radioActivity_description,
            };
            sequence.push( temp_array );

            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            if (
                retrievedLessonSequence.activity_id ||
                retrievedLessonSequence.activity_id > 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";
                $scope.activityHeader = "selSequencePartHeader";
                $scope.activityData = "selSequencePartBackground";

                $scope.activityName = retrievedLessonSequence.activity_name;
                $scope.activityDescription =
                    retrievedLessonSequence.activity_description;

                // get deliveries by bloom_id, webb_id, and activity_id
                let Obj = {
                    bloom_id: retrievedLessonSequence.bloom_id,
                    webb_id: retrievedLessonSequence.webb_id,
                    activity_id: radioActivityId,
                };

                let serviceBase = config.url;

                // POST request to activity endpoint, get activities by bloom and webb
                $http
                    .post( serviceBase + "delivery/getDeliveriesByBloomWebbActivity", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            $scope.deliveries = response.DATA;
                        }
                    } )
                    .error( function ( response ) {
                        $scope.deliveries;
                    } );
            } else {
                $scope.activityHeader = "selSequencePartHeader";
                $scope.activityData = "selSequencePartBackground";
            }

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        $scope.getDeliveryRadioValue = function (
            radioDeliveryId,
            radioDelivery_name,
            deliveryIdx
        ) {
            $scope.deilveryRadioIdxHideShow = deliveryIdx + 1;
            // set activity lesson sequence to 0, zeroed activity sequence
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            $scope.standardData = "";
            $scope.standards = {};

            /**
             * save zeroed delivery sequence to local storage
             * convert lessonSequence object into JSON string and save it to local storage
             */
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );

            $scope.deliveryListIdx = deliveryIdx;
            $scope.deliveryRadioIdxHideShow = deliveryIdx + 1;

            // get selected activity radio input value
            let inputs = document.getElementsByTagName( "inputs" );
            radioDelivery = document.getElementsByName( "deliveryList" );

            $scope.del = radioDelivery[ deliveryIdx ].value;

            //lessonSequence.delivery_name
            $scope.deliveryRadioIdx = deliveryIdx;
            $scope.deliveryHeader = "selSequencePartHeader";
            $scope.deliveryData = "selSequencePartBackground";

            lessonSequence.delivery_id = radioDeliveryId;
            lessonSequence.delivery_name = radioDelivery_name;

            //     let temp_array = {
            //         delivery: {
            //             id: radioDeliveryId,
            //             name: radioDelivery_name,}
            //     }
            //    sequence.push(temp_array);
            //    console.log("sequence length: ", sequence.length)
            //    console.log("sequence: ", sequence)

            let temp_array = {
                id: radioDeliveryId,
                definition: "delivery",
                name: radioDelivery_name,
                desc: "",
            };
            sequence.push( temp_array );

            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            if (
                retrievedLessonSequence.delivery_id ||
                retrievedLessonSequence.delivery_id > 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";
                $scope.activityHeader = "selSequencePartHeader";
                $scope.activityData = "selSequencePartBackground";
                $scope.deliveryHeader = "selSequencePartHeader";
                $scope.deliveryData = "selSequencePartBackground";
                $scope.deliveryName = retrievedLessonSequence.delivery_name;

                // get beyond the standards by delivery_id
                let Obj = {
                    bloom_id: retrievedLessonSequence.bloom_id,
                    webb_id: retrievedLessonSequence.webb_id,
                    activity_id: retrievedLessonSequence.activity_id,
                    delivery_id: radioDeliveryId,
                };

                let serviceBase = config.url;

                // POST request to activity endpoint, get activities by bloom and webb
                $http
                    .post( serviceBase + "standard/getStandardsByDelivery", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            $scope.standards = response.DATA;
                        }
                    } )
                    .error( function ( response ) {
                        $scope.standards;
                    } );
            } else {
                $scope.deliveryHeader = "selSequencePartHeader";
                $scope.deliveryData = "selSequencePartBackground";
            }

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        $scope.getStandardRadioValue = function (
            radioStandardId,
            radioStandard_name,
            standardIdx
        ) {
            // set activity lesson sequence to 0, zeroed activity sequence
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            $scope.modificationData = "";
            $scope.modifications = {};
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;

            /**
             * save zeroed standard sequence to local storage
             * convert lessonSequence object into JSON string and save it to local storage
             */
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            $scope.standardListIdx = standardIdx;
            $scope.standardRadioIdx = standardIdx;
            $scope.standardRadioIdxHideShow = standardIdx + 1;

            // get selected activity radio input value
            let inputs = document.getElementsByTagName( "inputs" );
            radioStandard = document.getElementsByName( "standardList" );

            $scope.std = radioStandard[ standardIdx ].value;

            $scope.standardRadioIdx = standardIdx;
            $scope.standardHeader = "selSequencePartHeader";
            $scope.standardData = "selSequencePartBackground";

            lessonSequence.standard_id = radioStandardId;
            lessonSequence.standard_name = radioStandard_name;

            //     let temp_array = {
            //         standards: {
            //             id: radioStandardId,
            //             name: radioStandardId,}
            //     }
            //    sequence.push(temp_array);
            //    console.log("sequence length: ", sequence.length)
            //    console.log("sequence: ", sequence)

            let temp_array = {
                id: radioStandardId,
                definition: "standard",
                name: radioStandard_name,
                desc: "",
            };
            sequence.push( temp_array );

            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            if (
                retrievedLessonSequence.standard_id ||
                retrievedLessonSequence.standard_id > 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";
                $scope.activityHeader = "selSequencePartHeader";
                $scope.activityData = "selSequencePartBackground";
                $scope.deliveryHeader = "selSequencePartHeader";
                $scope.deliveryData = "selSequencePartBackground";
                $scope.standardHeader = "selSequencePartHeader";
                $scope.standardData = "selSequencePartBackground";
                $scope.standardName = retrievedLessonSequence.standard_name;

                // get beyond the standards by delivery_id
                let Obj = {
                    bloom_id: retrievedLessonSequence.bloom_id,
                    webb_id: retrievedLessonSequence.webb_id,
                    activity_id: retrievedLessonSequence.activity_id,
                    delivery_id: retrievedLessonSequence.delivery_id,
                    beyond_id: radioStandardId,
                };

                let serviceBase = config.url;

                // POST request to modification  endpoint, get modifications by delivery
                $http
                    .post( serviceBase + "modification/getModificationsByStandards", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            $scope.modifications = response.DATA;
                        }
                    } )
                    .error( function ( response ) {
                        $scope.modifications;
                    } );
            } else {
                $scope.standardHeader = "selSequencePartHeader";
                $scope.standardData = "selSequencePartBackground";
            }
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        $scope.getModificationRadioValue = function (
            radioModificationId,
            radioModification_name,
            modificationIdx
        ) {
            $scope.modificationListIdx = modificationIdx;
            $scope.modificationRadioIdx = modificationIdx;
            $scope.modificationRadioIdxHideShow = modificationIdx + 1;

            // get selected modification radio input value
            let inputs = document.getElementsByTagName( "inputs" );
            radioModification = document.getElementsByName( "modificationList" );

            // get value of selected modification radio button
            $scope.mod = radioModification[ modificationIdx ].value;

            // save user selected modification to local lessonSequence object
            lessonSequence.modification_id = radioModificationId;
            lessonSequence.modification_name = radioModification_name;

            //     let temp_array = {
            //         modifications: {
            //             id: radioModificationId,
            //             name: radioModification_name,}
            //     }
            //    sequence.push(temp_array);
            //    console.log("sequence length: ", sequence.length)
            //    console.log("sequence: ", sequence)

            let temp_array = {
                id: radioModificationId,
                definition: "modification",
                name: radioModification_name,
                desc: "",
            };
            sequence.push( temp_array );
            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            $scope.modification = modificationIdx;
            $scope.modificationHeader = "selSequencePartHeader";
            $scope.modificationData = "selSequencePartBackground";

            /**
             *  save local lessonSequence object to local storage
             * convert local lessonSequence object into JSON string and save it
             */
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            // if there is modification_id and modification_name change header and background color
            if (
                retrievedLessonSequence.modification_id ||
                retrievedLessonSequence.modification_id > 0
            ) {
                $scope.bloomWebbHeader = "selSequencePartHeader";
                $scope.bloomWebbData = "selSequencePartBackground";
                $scope.activityHeader = "selSequencePartHeader";
                $scope.activityData = "selSequencePartBackground";
                $scope.deliveryHeader = "selSequencePartHeader";
                $scope.deliveryData = "selSequencePartBackground";
                $scope.standardHeader = "selSequencePartHeader";
                $scope.standardData = "selSequencePartBackground";
                $scope.modificationHeader = "selSequencePartHeader";
                $scope.modificationData = "selSequencePartBackground";
                $scope.modificationName = retrievedLessonSequence.modification_name;
                //$scope.hideLessonSequence=0
            }
        };

        /**
         * reset bloom webb selection
         */
        $scope.resetBlooms = function () {
            $scope.bloomRadioIdx = -1;
            $scope.webbListIdx = -1;
            $scope.deliveryIdx = 0;
            deliveryIdx = 0;
            sequence = [];
            $scope.bloomWebbHeader = "";
            $scope.bloomWebbData = "";
            $scope.activityHeader = "";
            $scope.activityData = "";
            $scope.deliveryHeader = "";
            $scope.deliveryData = "";
            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.getAllSequenceData();
            // document.getElementsByName( "bloomList" ).checked = false;
            document.getElementsByName( "activityList" ).checked = false;
            document.getElementsByName( "deliveryList" ).checked = false;
            document.getElementsByName( "standardList" ).checked = false;
            document.getElementsByName( "modificationList" ).checked = false;
            $scope.bloomWebbHeader = "";
            $scope.activityHeader = "";
            $scope.deliveryHeader = "";
            $scope.standardHeader = "";
            $scope.modificationHeader = "";
            $scope.bloomWebbData = "";
            $scope.activityData = "";
            $scope.deliveryData = "";
            $scope.standardData = "";
            $scope.modificationData = "";
            $scope.allBloomsWebbs = [];
            lessonSequence.webb_id = 0;
            lessonSequence.webb_description = 0;
            $scope.blooms = {};
            $scope.activities = {};
            $scope.deliveries = {};
            $scope.standards = {};
            $scope.modifications = {};
            $scope.webbIdResult = 0;
            $scope.activityRadioIdx = 0;
            $scope.activityRadioIdxHideShow = 0;
            $scope.deliveryRadioIdx = 0;
            $scope.deliveryIdx = 0;
            $scope.hideBloomWebbs = "0";
            $scope.hideActivities = "0";
            $scope.hideDeliveries = "0";
            $scope.hideBeyond = "0";
            $scope.hideModifications = "0";
            lessonSequence.activity_id = "0";
            lessonSequence.activity_name = 0;
            lessonSequence.activity_description = 0;
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            sequence = [];

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
            var serviceBase = config.url;

            // Get all bloom's
            $http
                .post( serviceBase + "bloom/getAllBlooms" )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.blooms = response.DATA;
                        $http
                            .post( serviceBase + "webb/getWebbByBloom", {
                                ignoreLoadingBar: true,
                            } )
                            .success( function ( response ) {
                                if ( response.STATUS == 1 ) {
                                    webbsList = angular.fromJson( response.DATA );
                                    webbsListNew = webbsList;
                                    $scope.webbsList = webbsList;
                                    $scope.allBloomsWebbs = webbsList;
                                } else {
                                    $scope.webbs = "";
                                }
                            } )
                            .error( function ( response ) {
                                $scope.webbs = "";
                            } );
                    }
                } );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        /**
         * reset activities selection
         */
        $scope.resetActivities = function () {
            $scope.activityHeader = "";
            $scope.activityData = "";
            $scope.deliveryHeader = "";
            $scope.deliveryData = "";
            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            document.getElementsByName( "activityList" ).checked = false;
            document.getElementsByName( "deliveryList" ).checked = false;
            document.getElementsByName( "standardList" ).checked = false;
            document.getElementsByName( "modificationList" ).checked = false;
            $scope.activityHeader = "";
            $scope.activityData = "";
            $scope.deliveryHeader = "";
            $scope.deliveryData = "";
            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            lessonSequence.activity_id = 0;
            lessonSequence.activity_name = 0;
            lessonSequence.activity_description = 0;
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;
            $scope.deliveries = {};
            $scope.standards = {};
            $scope.modifications = {};
            $scope.activityRadioIdx = 0;
            $scope.deliveryRadioIdx = 0;
            $scope.standardRadioIdx = 0;
            $scope.modificationRadioIdx = 0;
            $scope.activityRadioIdxHideShow = 0;
            $scope.hideActivities = "0";
            $scope.hideDeliveries = "0";
            $scope.hideBeyond = "0";
            $scope.hideModifications = "0";
            lessonSequence.activity_id = "0";

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            // Obj arg for delivery endpoint
            let Obj = {
                bloom_id: retrievedLessonSequence.bloom_id,
                webb_id: retrievedLessonSequence.webb_id,
                //"activity_id": retrievedLessonSequence.activity_id,
            };

            let serviceBase = config.url;

            // POST request to delivery endpoint, get delivery by bloom, webb, activity
            $http
                .post( serviceBase + "activity/getActivitiesByBloomAndWebb", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.activities = response.DATA;
                    }
                } )
                .error( function ( response ) {
                    $scope.activities;
                } );

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        /**
         * reset delivery section
         */
        $scope.resetDeliveries = function () {
            document.getElementsByName( "deliveryList" ).checked = false;
            document.getElementsByName( "standardList" ).checked = false;
            document.getElementsByName( "modificationList" ).checked = false;
            $scope.deliveryHeader = "";
            $scope.deliveryData = "";
            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            lessonSequence.delivery_id = 0;
            lessonSequence.delivery_name = 0;
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;
            $scope.standards = {};
            $scope.modifications = {};
            $scope.deliveryRadioIdx = 0;
            $scope.standardRadioIdx = 0;
            $scope.modificationRadioIdx = 0;
            $scope.deliveryRadioIdxHideShow = 0;

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            // Object arg for modification endpoint
            let Obj = {
                bloom_id: retrievedLessonSequence.bloom_id,
                webb_id: retrievedLessonSequence.webb_id,
                activity_id: retrievedLessonSequence.activity_id,
                //"delivery_id": retrievedLessonSequence.activity_id,
            };

            let serviceBase = config.url;

            // POST request to activity endpoint, get activities by bloom and webb
            $http
                .post( serviceBase + "delivery/getDeliveriesByBloomWebbActivity", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.deliveries = response.DATA;
                    }
                } )
                .error( function ( response ) {
                    $scope.deliveries;
                } );

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        /**
         * reset standards section
         */
        $scope.resetStandards = function () {
            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            document.getElementsByName( "standardList" ).checked = false;
            document.getElementsByName( "modificationList" ).checked = false;

            //document.getElementById( 'standardSequenceData' ).style.visibility = 'visible';
            //document.getElementById( 'modificationSequenceData' ).style.visibility = 'hidden';

            $scope.standardHeader = "";
            $scope.standardData = "";
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            lessonSequence.standard_id = 0;
            lessonSequence.standard_name = 0;
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;
            $scope.modifications = {};
            $scope.standardIdx = 0;
            $scope.standardRadioIdx = 0;
            $scope.standardRadioIdxHideShowInput = 0;
            $scope.standardRadioIdxHideShow = 0;
            $scope.modificationRadioIdx = 0;
            $scope.modificationRadioIdxHideShow = 0;
            $scope.hideStandards = "0";
            $scope.hideModifications = "0";
            $scope.hideBeyond = "0";

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            // Object arg for modification endpoint
            let Obj = {
                bloom_id: retrievedLessonSequence.bloom_id,
                webb_id: retrievedLessonSequence.webb_id,
                activity_id: retrievedLessonSequence.activity_id,
                delivery_id: retrievedLessonSequence.delivery_id,
                beyond_id: retrievedLessonSequence.standard_id,
            };

            let serviceBase = config.url;

            // POST request to modification  endpoint, get modifications by delivery
            $http
                .post( serviceBase + "standard/getStandardsByDelivery", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.standards = response.DATA;
                    }
                } )
                .error( function ( response ) {
                    $scope.standards;
                } );
            // document.getElementById( 'modificationSequenceData' ).style.visibility = 'visible';

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        /**
         * reset modification section
         */
        $scope.resetModifications = function () {
            $scope.modificationHeader = "";
            $scope.modificationData = "";
            document.getElementsByName( "modificationList" ).checked = false;

            // document.getElementById( "modificationSequenceData" ).style.visibility =
            //     "visible";
            // document.getElementById(
            //     "modificationSequenceDataResult"
            // ).style.visibility = "hidden";

            $scope.modificationHeader = "";
            $scope.modificationData = "";
            lessonSequence.modification_id = 0;
            lessonSequence.modification_name = 0;
            $scope.modificationRadioIdx = 0;
            $scope.modificationRadioIdxHideShow = 0;
            $scope.hideModifications = "0";
            // $scope.standardRadioIdxHideShowInput = 0;

            // convert lessonSequence object into JSON string and save it to storage
            localStorage.setItem(
                "localLessonSequence",
                JSON.stringify( lessonSequence )
            );
            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );

            // Object arg for modification endpoint
            let Obj = {
                bloom_id: retrievedLessonSequence.bloom_id,
                webb_id: retrievedLessonSequence.webb_id,
                activity_id: retrievedLessonSequence.activity_id,
                delivery_id: retrievedLessonSequence.delivery_id,
                beyond_id: retrievedLessonSequence.standard_id,
            };

            let serviceBase = config.url;

            // POST request to modification  endpoint, get modifications by delivery
            $http
                .post( serviceBase + "modification/getModificationsByStandards", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.modifications = response.DATA;
                    }
                } )
                .error( function ( response ) {
                    $scope.modifications;
                } );

            // retrieve the JSON string from local storage then parse JSON string back to JS object
            retrievedLessonSequence = JSON.parse(
                localStorage.getItem( "localLessonSequence" )
            );
        };

        $scope.$on( "$routeChangeStart", function ( next, current ) {
            cfpLoadingBar.start();
        } );
        /* Load Tab */
        $scope.tabs = [ {
                title: "Dynamic Title 1",
                content: "Dynamic content 1",
            },
            {
                title: "Dynamic Title 2",
                content: "Dynamic content 2",
                disabled: true,
            },
            {
                title: "Dynamic Title 3",
                content: "Dynamic content 3",
                disabled: true,
            },
        ];
        let timeout = null;

        // New Modal
        $scope.showForm = function () {
            var modalInstance = $uibModal.open( {
                templateUrl: "public/app/templates/site/partials/lessonSequenceModal.html",
                controller: ModalInstanceCtrl,
                windowClass: "app-modal-window",
                scope: $scope,
                resolve: {
                    userForm: function () {
                        return $scope.userForm;
                    },
                },
            } );
        };

        var ModalInstanceCtrl = function ( $scope, $uibModalInstance, userForm ) {
            $scope.form = {};
            $scope.submitForm = function () {
                if ( $scope.form.userForm.$valid ) {
                    console.log( "user form is in scope" );
                    $uibModalInstance.close( "closed" );
                } else {
                    console.log( "userform is not in scope" );
                }
            };

            $scope.cancel = function () {
                $uibModalInstance.dismiss( "cancel" );
            };
        };

        $scope.MakeBold = function () {
            if ( $scope.classBold.length == 0 ) {
                $scope.classBold = "blue-background";
            } else {
                $scope.classBold = "";
            }
        };

        // start CODE FROM MODIFICATION CTRL //
        $scope.redirect = function ( redirectpath ) {
            if ( !$scope.disabletab ) {
                if (
                    redirectpath == "modification/create" ||
                    redirectpath == "outline/create"
                ) {
                    if ( redirectpath == "modification/create" ) {
                        var local_summary = $scope.editor_summary || "";
                        var local_standards = $scope.editor_standards || "";
                        var local_essential_questions =
                            $scope.editor_essential_questions || "";
                        var local_core_ideas = $scope.editor_core_ideas || "";
                        var local_vocabulary = $scope.editor_vocabulary || "";
                        var local_lesson_sequence = $scope.editor_lesson_sequence || "";

                        if (
                            local_summary ||
                            local_standards ||
                            local_essential_questions ||
                            local_core_ideas ||
                            local_vocabulary ||
                            local_lesson_sequence
                        ) {
                            $scope.modi_re = true;
                        } else {
                            if ( localStorageService.get( "ispreview" ) ) {} else {
                                alert( "Please add result for modify." );
                                return false;
                            }
                        }
                    } else {
                        $scope.modi_re = false;
                    }
                    $scope.classdiv = false;
                }
                var link = localStorageService.get( "disblelink" );
                if ( link == "enable" && $scope.disblelink == "enable" ) {
                    if (
                        confirm(
                            "Are you sure you want to leave page without saving your changes?"
                        )
                    ) {
                        $location.path( redirectpath );
                    } else {
                        $scope.classdiv = true;
                    }
                } else {
                    $location.path( redirectpath );
                    $scope.classdiv = true;
                }
            }
            return false;
        };

        $scope.setPreview = function () {
            if ( $scope.disabletab ) {
                return false;
            } else {
                var local_summary = $scope.editor_summary || "";
                var local_standards = $scope.editor_standards || "";
                var local_essential_questions = $scope.editor_essential_questions || "";
                var local_core_ideas = $scope.editor_core_ideas || "";
                var local_vocabulary = $scope.editor_vocabulary || "";
                var local_modification = $scope.lesson_modification || "";
                var local_lesson_sequence = $scope.editor_lesson_sequence || "";
                if (
                    local_modification ||
                    local_summary ||
                    local_standards ||
                    local_essential_questions ||
                    local_core_ideas ||
                    local_vocabulary ||
                    local_lesson_sequence
                ) {
                    // $scope.modi_re = true;
                } else {
                    alert( "Please add result for preview." );
                    return false;
                }
            }
            var link = localStorageService.get( "disblelink" );
            if ( link == "enable" && $scope.disblelink == "enable" ) {
                if (
                    confirm(
                        "Are you sure you want to leave page without saving your changes?"
                    )
                ) {
                    //  $location.path(redirectpath);
                } else {
                    return true;
                }
            }

            var local_store = localStorageService.get( "lesson_modification" ) || "";
            if ( local_store == "" && !$scope.lesson_modification ) {
                localStorageService.set( "editor_summary", $scope.editor_summary );
                localStorageService.set( "editor_standards", $scope._standards );
                localStorageService.set(
                    "editor_essential_questions",
                    $scope.editor_essential_questions
                );
                localStorageService.set( "editor_core_ideas", $scope.editor_core_ideas );
                localStorageService.set( "editor_vocabulary", $scope.editor_vocabulary );
                localStorageService.set(
                    "editor_lesson_sequence",
                    $scope.editor_lesson_sequence
                );
            } else {
                if ( $scope.lesson_modification ) {
                    localStorageService.set(
                        "lesson_modification",
                        $scope.lesson_modification
                    );
                }
            }
            localStorageService.set( "ispreview", true );
            $location.path( "/preview/create" );
            $scope.checkurl_preview();
        };

        $scope.getPreview = function () {
            $scope.lesson_modification = localStorageService.get(
                "lesson_modification"
            );
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( "last_lession_id" );
            lession_id = angular.fromJson( lession_id );

            if ( !$scope.lesson_modification ) {
                var last_lession_id = $cookieStore.get( "last_lession_id" );
                var learning_targets = localStorageService.get( "Learning_target" );
                var Obj = {
                    last_lession_id: last_lession_id.last_id,
                };

                var serviceBase = config.url;
                $http
                    .post( serviceBase + "learningtarget/modification", Obj )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {
                            if ( response.DATA.content != "" ) {
                                $scope.lesson_modification = response.DATA.content;
                                var ObjPdf = {
                                    lesson_id: lession_id.last_id,
                                    user_id: user_id,
                                    temp_content: $scope.lesson_modification,
                                };

                                var serviceBase = config.url;
                                $http
                                    .post( serviceBase + "LessonUser/saveTempPdfData", ObjPdf )
                                    .success( function ( response ) {
                                        if ( response.STATUS == 1 ) {
                                            console.log( "OK" );
                                        } else {
                                            Flash.create( "danger", response.MESSAGE );
                                            console.log( "BAD" );
                                        }
                                    } );
                            }
                        }
                    } );
            } else {
                var ObjPdf = {
                    lesson_id: lession_id.last_id,
                    user_id: user_id,
                    temp_content: $scope.lesson_modification,
                };
                var serviceBase = config.url;
                $http
                    .post( serviceBase + "LessonUser/saveTempPdfData", ObjPdf )
                    .success( function ( response ) {
                        if ( response.STATUS == 1 ) {} else {
                            Flash.create( "danger", response.MESSAGE );
                        }
                    } );
            }
        };

        // download PDF
        $scope.downloadPdf = function ( printSectionId ) {
            console.log( "CLICK!!!" );
            $scope.tableFinished = "0";
            // get the content of html element to print
            let innerContents = document.getElementById( "lessonSequencePdfTable" )
                .innerHTML;

            // open popup window to draw html
            let popupWinindow = window.open(
                "",
                "_blank",
                "width=1280,height=900,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no"
            );
            popupWinindow.document.open();

            // embed your css file and CDN css file into head, embed html content into body
            popupWinindow.document.write(
                '<html><head><link rel="stylesheet" type="text/css" href="public/app/build/css/downloadPdf.css" /></head><body onload="window.print()">' +
                innerContents +
                "</html>"
            );
            popupWinindow.document.close();
        };

        $scope.checkurl_preview = function () {
            var path = $location.path();
            if ( path === "/preview/create" ) {
                return "disable-link";
            }
            return "";
        };

        $scope.modification = function ( flag ) {
            var link = localStorageService.get( "disblelink" );
            // if (link == "enable" && $scope.disblelink == "enable") {
            if ( $scope.checkurl() ) {
                $scope.saveModifacation( flag );
            } else {
                $scope.saveOutline( flag );
            }
            // }
        };

        $scope.checkurl = function () {
            var path = $location.path();
            if ( path === "/modification/create" ) {
                return true;
            }
            return false;
        };

        // this is the main save function
        $scope.saveOutline = function ( flag ) {
            var keyconcept_id = localStorageService.get( "key_concepts" );
            var learningtarget_id = localStorageService.get( "Learning_target" );
            // var lesson_sequence_id = localStorageService.get("Lesson_sequence");
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( "last_lession_id" );
            lession_id = angular.fromJson( lession_id );
            var summary_box = localStorageService.get( "summary_box" );
            if ( learningtarget_id.length == 0 || keyconcept_id.length == 0 ) {
                $scope.editor_summary = "";
                $scope.editor_standards = "";
                $scope.editor_essential_questions = "";
                $scope.editor_core_ideas = "";
                $scope.editor_lesson_sequence = "";
            }

            var Obj = {
                keyconcept_id: keyconcept_id,
                learningtarget_id: learningtarget_id,
                lesson_id: lession_id.last_id,
                user_id: user_id,
                summary: $scope.editor_summary,
                standards: $scope.editor_standards,
                essential_questions: $scope.editor_essential_questions,
                core_ideas: $scope.editor_core_ideas,
                vocabulary: $scope.editor_vocabulary,
                lesson_sequence: $scope.editor_lesson_sequence,
                summary_box: summary_box,
            };

            var serviceBase = config.url;
            $http
                .post( serviceBase + "LessonUser/createLessonByUser", Obj, {
                    ignoreLoadingBar: true,
                } )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.disblelink = "disable-link";
                        localStorageService.set( "disblelink", "disable-link" );
                        // Flash.create('success', response.MESSAGE);

                        if ( learningtarget_id.length <= 0 && $scope.emptyDone != "yes" ) {
                            $scope.lesson_modification == "";
                            $scope.emptyDone = "yes";
                            $scope.saveModifacation();
                        } else {
                            if ( flag == "flag" ) {} else {
                                Flash.create( "success", "Template saved!" );
                            }
                        }
                    } else {
                        Flash.create( "danger", response.MESSAGE );
                    }
                } )
                .error( function ( response ) {} );
        };

        $scope.saveModifacation = function ( flag ) {
            var learningtarget_id = localStorageService.get( "Learning_target" );
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( "last_lession_id" );
            lession_id = angular.fromJson( lession_id );

            var Obj = {
                lesson_id: lession_id.last_id,
                user_id: user_id,
                content: $scope.lesson_modification,
            };

            var serviceBase = config.url;
            $http
                .post( serviceBase + "LessonUser/savemodification", Obj, {
                    ignoreLoadingBar: true,
                } )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        $scope.disblelink = "disable-link";
                        localStorageService.set( "disblelink", "disable-link" );
                        if ( learningtarget_id.length <= 0 && $scope.emptyDone != "yes" ) {
                            $scope.editor_summary = "";
                            $scope.editor_standards = "";
                            $scope.editor_essential_questions = "";
                            $scope.editor_core_ideas = "";
                            $scope.editor_lesson_sequence = "";
                            $scope.emptyDone = "yes";
                            $scope.saveOutline();
                        } else {
                            if ( flag == "flag" ) {} else {
                                Flash.create( "success", "Template saved!" );
                            }
                        }
                    } else {
                        Flash.create( "danger", response.MESSAGE );
                    }
                } )
                .error( function ( response ) {} );
        };
        // close CODE FROM MODIFICATION CTRL //
        $scope.blur = "noblur";
        $scope.click = "noclick";
        $scope.jqueryScrollbarOptions = {
            autoScrollSize: false,
            scrolly: $( ".external-scroll_y" ),
        };
        $scope.jqueryScrollbarOptions2 = {
            autoScrollSize: false,
            scrolly: $( ".external-scroll_yy" ),
        };

        // $scope.loadTiny = function () {
        $scope.tinymceOptions = {
            setup: function ( e ) {
                e.on( "init", function ( a, b ) {
                    $scope.disblelink = "disable-link";
                    localStorageService.set( "disblelink", "disable-link" );
                    var fn = e.windowManager.open;
                    // override with your own version of the function
                    e.windowManager.open = function ( t, r ) {
                        // make sure you only target the 'insert link' dialog
                        if ( t.title == "Insert link" ) {
                            // reference to the submit function of the dialog
                            var oldsubmit = t.onSubmit;
                            // override the submit function
                            t.onSubmit = function ( es ) {
                                // append the "http://" prefix here, note that the URL is contained within the property 'href' of data.
                                // also see link/plugin.js
                                if ( !es.data.href.match( /(ftp|https?):\/\//i ) ) {
                                    es.data.href = "http://" + es.data.href;
                                }
                                // submit original function
                                return oldsubmit( es );
                            };
                            // after overwriting the submit function within the windowManager, make sure to call the original function
                            fn.apply( this, [ t, r ] );
                        }
                        // use return instead of apply to prevent bugs in other dialogs
                        else {
                            return fn( t, r );
                        }
                    };
                } );
                e.on( "change", function ( a ) {
                    if ( $scope.click == "click" ) {
                        $scope.disblelink = "enable";
                        localStorageService.set( "disblelink", "enable" );
                        $timeout( function () {
                            $scope.modification( "flag" );
                        }, 2000 );
                    } else {
                        //                        console.log('unnecessary called');
                    }
                } );
                // Update model on keypress
                e.on( "click", function ( e ) {
                    $scope.click = "click";
                } );
                e.on( "redo", function ( a, b ) {
                    $scope.disblelink = "enable";
                    localStorageService.set( "disblelink", "enable" );
                    $timeout( function () {
                        $scope.modification( "flag" );
                    }, 2000 );
                } );

                e.on( "undo", function ( a, b ) {
                    $scope.disblelink = "enable";
                    localStorageService.set( "disblelink", "enable" );
                    $timeout( function () {
                        $scope.modification( "flag" );
                    }, 2000 );
                } );
                e.on( "blur", function ( a, b ) {
                    $scope.click = "noclick";
                } );
            },
            theme: "modern",
            relative_urls: false,
            menubar: false,
            statusbar: false,
            height: 210,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor responsivefilemanager code",
            ],
            toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect | code",
            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview | cut paste | undo redo",
            image_advtab: false,
            external_filemanager_path: "/filemanager/",
            filemanager_title: "Responsive Filemanager",
            default_link_target: "_blank",
            convert_urls: false,
            external_plugins: {
                filemanager: "/filemanager/plugin.min.js",
            },
        };
        // }
        // $scope.loadTinyModi = function () {
        $scope.tinymceOptionsModi = {
            setup: function ( e ) {
                e.on( "init", function ( a, b ) {
                    $scope.disblelink = "disable-link";
                    localStorageService.set( "disblelink", "disable-link" );
                    var fn = e.windowManager.open;
                    // override with your own version of the function
                    e.windowManager.open = function ( t, r ) {
                        // make sure you only target the 'insert link' dialog
                        if ( t.title == "Insert link" ) {
                            // reference to the submit function of the dialog
                            var oldsubmit = t.onSubmit;
                            // override the submit function
                            t.onSubmit = function ( es ) {
                                // append the "http://" prefix here, note that the URL is contained within the property 'href' of data.
                                // also see link/plugin.js
                                if ( !es.data.href.match( /(ftp|https?):\/\//i ) ) {
                                    es.data.href = "http://" + es.data.href;
                                }
                                // submit original function
                                return oldsubmit( es );
                            };
                            // after overwriting the submit function within the windowManager, make sure to call the original function
                            fn.apply( this, [ t, r ] );
                        }
                        // use return instead of apply to prevent bugs in other dialogs
                        else {
                            return fn( t, r );
                        }
                    };
                } );
                e.on( "change", function ( a ) {
                    if ( $scope.click == "click" ) {
                        $scope.disblelink = "enable";
                        localStorageService.set( "disblelink", "enable" );
                        $timeout( function () {
                            $scope.modification( "flag" );
                        }, 2000 );
                    } else {}
                } );
                // Update model on keypress
                e.on( "click", function ( e ) {
                    $scope.click = "click";
                } );
                e.on( "redo", function ( a, b ) {
                    $scope.disblelink = "enable";
                    localStorageService.set( "disblelink", "enable" );
                    $timeout( function () {
                        $scope.modification( "flag" );
                    }, 2000 );
                } );

                e.on( "undo", function ( a, b ) {
                    $scope.disblelink = "enable";
                    localStorageService.set( "disblelink", "enable" );
                    $timeout( function () {
                        $scope.modification( "flag" );
                    }, 2000 );
                } );
                e.on( "blur", function ( a, b ) {
                    $scope.click = "noclick";
                } );
            },
            theme: "modern",
            relative_urls: false,
            menubar: false,
            statusbar: false,
            height: 500,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor responsivefilemanager code",
            ],
            toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code | undo redo",
            image_advtab: false,
            external_filemanager_path: "/filemanager/",
            filemanager_title: "Responsive Filemanager",
            default_link_target: "_blank",
            convert_urls: false,
            external_plugins: {
                filemanager: "/filemanager/plugin.min.js",
            },
        };
        // }
        // $scope.loadTiny();
        //     $scope.loadTinyModi();

        $scope.model = {
            name: "Tabs",
        };
        $scope.test = "";
        $scope.disabletab = true;
        $scope.smbxdiv = $scope.stbxdiv = $scope.enbxdiv = $scope.cibxdiv = $scope.vbbxdiv = false;
        $scope.emptybxdiv = true;

        var checkboxes_run = localStorageService.get( "Learning_target" );
        if ( angular.isArray( checkboxes_run ) ) {
            if ( checkboxes_run.length > 0 ) {
                $scope.disabletab = false;
            }
        }
        /* Use : get learning target */
        $scope.fixLoad = function () {
            $scope.classdiv = false;
        };

        $scope.getLessonSequenceBloom = function ( id, bloom_name ) {
            // Set variable to api/
            var serviceBase = `config.url;`;
            // Get last session cookie , set equal to variable
            var last_lession_id = $cookieStore.get( "last_lession_id" );

            // Check if bloom's name
            if ( !angular.isDefined( bloom_name ) ) {
                bloom_name = false;
            }
        };

        // Start of  Learning Targets
        $scope.getLearningTarget = function ( id, keyconcept ) {
            var serviceBase = `config.url;`;
            var last_lession_id = $cookieStore.get( "last_lession_id" );

            if ( !angular.isDefined( keyconcept ) ) {
                keyconcept = false;
            }

            var checkboxes = localStorageService.get( "key_concepts" );
            if ( angular.isDefined( keyconcept ) ) {
                if ( keyconcept ) {
                    if ( !angular.isArray( checkboxes ) ) {
                        var checkarr = [];
                        checkarr.push( id );
                        localStorageService.set( "key_concepts", checkarr );
                    } else {
                        if ( checkboxes.indexOf( id ) == -1 ) {
                            checkboxes.push( id );
                        }
                        localStorageService.set( "key_concepts", checkboxes );
                    }
                } else {
                    var index = checkboxes.indexOf( id );
                    if ( index >= 0 ) checkboxes.splice( index, 1 );

                    localStorageService.set( "key_concepts", checkboxes );
                }
            }

            var checkboxes = localStorageService.get( "key_concepts" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                keyconcept_id: id,
            };
            $http
                .post( "api/lesson/getLearningTargetDetails", Obj, {
                    ignoreLoadingBar: true,
                } )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        var learning_targets = angular.fromJson( response.DATA );

                        $scope.learning_target =
                            learning_targets[ 0 ].learning_target_details;
                    } else {
                        $scope.learning_target = "";
                    }
                } )
                .error( function ( response ) {
                    $scope.learning_target = "";
                } );
        };

        // Toggle Blooms selection
        $scope.toggleBloomSelection = function ( ids, targetselected ) {
            if ( !angular.isDefined( targetselected ) ) {
                targetselected = false;
            }
            var checkboxes = localStorageService.get( "webbs" );

            // ! Is newly selected
            if ( targetselected ) {
                if ( !angular.isArray( checkboxes ) ) {
                    var checkarr = [];
                    checkarr.push( ids );
                    localStorageService.set( "webbs", checkarr );
                } else {
                    if ( checkboxes.indexOf( ids ) == -1 ) {
                        checkboxes.push( ids );
                    }
                    localStorageService.set( "webbs", checkboxes );
                }

                // ! Is currently selected
            } else {
                var index = checkboxes.indexOf( ids );
                if ( index > -1 ) checkboxes.splice( index, 1 );
                localStorageService.set( "webbs", checkboxes );
            }

            var checkboxes = localStorageService.get( "webbs" );

            if ( checkboxes.length > 0 ) {
                $scope.disabletab = false;
                $scope.emptybxdiv = false;
            } else {
                $scope.disablelink = "enable";
                localStorageService.set( "disablelink", "enable" );
                $scope.diabletab = true;
                $scope.sumarybox = "";
                localStorageService.set( "summarybox", "" );
                $scope.standardbox = "";
                localStorageService.set( "standardbox", "" );
                $scope.essentialbox = "";
                localStorageService.set( "essentialbox", "" );
                $scope.core_ideasbox = "";
                localStorageService.set( "core_ideasbox", "" );
                $scope.vocabulariesbox = "";
                localStorageService.set( "vocabulariesbox", "" );
                $scope.lesson_modification = "";
                localStorageService.set( "lesson_modification", "" );
                $scope.smbbxdiv = $scope.stbxdiv = $scope.enbxdiv = $scope.cibxdiv = $scope.vbbxdiv = false;
                $scope.emptybxdiv = true;
            }
        };

        $scope.toggleSelection = function ( ids, targetselected ) {
            console.log( "TARGET" );

            if ( !angular.isDefined( targetselected ) ) {
                targetselected = false;
            }
            var checkboxes = localStorageService.get( "Learning_target" );

            if ( angular.isDefined( targetselected ) ) {
                if ( targetselected ) {
                    if ( !angular.isArray( checkboxes ) ) {
                        var checkarr = [];
                        checkarr.push( ids );
                        localStorageService.set( "Learning_target", checkarr );
                        console.log( "Learning Target checkarr: ", checkarr );
                    } else {
                        if ( checkboxes.indexOf( ids ) == -1 ) {
                            checkboxes.push( ids );
                        }
                        localStorageService.set( "Learning_target", checkboxes );
                        console.log( "Learning Target checkboxes1: ", checkboxes );
                    }
                } else {
                    var index = checkboxes.indexOf( ids );
                    if ( index >= 0 ) checkboxes.splice( index, 1 );
                    localStorageService.set( "Learning_target", checkboxes );
                    console.log( "Learning Target checkboxes2: ", checkboxes );
                }
            }

            var checkboxes = localStorageService.get( "Learning_target" );

            if ( checkboxes.length > 0 ) {
                $scope.disabletab = false;
                $scope.emptybxdiv = false;
            } else {
                $scope.disblelink = "enable";
                localStorageService.set( "disblelink", "enable" );
                //                localStorageService.set("lesson_modification",'')
                $scope.disabletab = true;
                $scope.summarybox = "";
                localStorageService.set( "summarybox", "" );
                $scope.standardbox = "";
                localStorageService.set( "standardbox", "" );
                $scope.essentialbox = "";
                localStorageService.set( "essentialbox", "" );
                $scope.core_ideasbox = "";
                localStorageService.set( "core_ideasbox", "" );
                $scope.vocabulariesbox = "";
                localStorageService.set( "vocabulariesbox", "" );
                $scope.lesson_modification = "";
                localStorageService.set( "lesson_modification", "" );
                $scope.smbxdiv = $scope.stbxdiv = $scope.enbxdiv = $scope.cibxdiv = $scope.vbbxdiv = false;
                $scope.emptybxdiv = true;
            }
        };
        var secondsToWaitBeforeSave = 2;

        var debounceSaveUpdates = function ( newVal, oldVal ) {
            if ( newVal != oldVal ) {
                if ( timeout ) {
                    $timeout.cancel( timeout );
                }
                timeout = $timeout(
                    $scope.modification( "flag" ),
                    secondsToWaitBeforeSave * 2000
                ); // 1000 = 1 second
            }
        };

        $scope.loadKeyconcept = function () {
            $scope.loadresult = true;
            localStorageService.set( "ispreview", false );
            var targets = localStorageService.get( "Learning_target" );
            var serviceBase = config.url;
            var Obj = {
                learningtarget_id: targets,
            };
            $http
                .post( serviceBase + "learningtarget/getLearningTargetById", Obj, {
                    ignoreLoadingBar: true,
                } )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        var lessondata = angular.fromJson( response.DATA );
                        console.log( "lessondata: ", lessondata );
                        $scope.lessonDataRes = lessondata;

                        var summary = [];
                        var standards = [];
                        var essential_questions = [];
                        var core_ideas = [];
                        var vocabulary = [];
                        let lesson_sequence = [];
                        //Assign flag for empty records
                        var summary_flag = false;
                        var standards_flag = false;
                        var essential_questions_flag = false;
                        var core_ideas_flag = false;
                        var vocabulary_flag = false;
                        let lesson_sequence_flag = false;

                        angular.forEach( lessondata, function ( value, key ) {
                            var summaryobj = {
                                id: "",
                                name: "",
                                desc: "",
                                keyconcept_id: "",
                                kc_name: "",
                                display: true,
                            };
                            if ( value.overview_summary != "" ) {
                                //Assign flag for empty records
                                summary_flag = true;
                                summaryobj = {
                                    id: value.id,
                                    name: value.name,
                                    desc: value.overview_summary,
                                    keyconcept_id: value.keyconcepts_id,
                                    kc_name: value.kc_name,
                                    display: false,
                                };
                                summary[ "" ];
                            }
                            summary[ key ] = summaryobj;

                            var standardobj = {
                                id: "",
                                name: "",
                                desc: "",
                                keyconcept_id: "",
                                kc_name: "",
                                display: true,
                            };
                            if ( value.standards != "" ) {
                                //Assign flag for empty records
                                standards_flag = true;
                                standardobj = {
                                    id: value.id,
                                    name: value.name,
                                    desc: value.standards,
                                    keyconcept_id: value.keyconcepts_id,
                                    kc_name: value.kc_name,
                                    display: false,
                                };
                            }
                            standards[ key ] = standardobj;

                            var essential_questionsobj = {
                                id: "",
                                name: "",
                                desc: "",
                                keyconcept_id: "",
                                kc_name: "",
                                display: true,
                            };
                            if ( value.essential_questions != "" ) {
                                //Assign flag for empty records
                                essential_questions_flag = true;
                                essential_questionsobj = {
                                    id: value.id,
                                    name: value.name,
                                    desc: value.essential_questions,
                                    keyconcept_id: value.keyconcepts_id,
                                    kc_name: value.kc_name,
                                    display: false,
                                };
                            }
                            essential_questions[ key ] = essential_questionsobj;

                            var core_ideasobj = {
                                id: "",
                                name: "",
                                desc: "",
                                keyconcept_id: "",
                                kc_name: "",
                                display: true,
                            };
                            if ( value.core_ideas != "" ) {
                                //Assign flag for empty records
                                core_ideas_flag = true;
                                core_ideasobj = {
                                    id: value.id,
                                    name: value.name,
                                    desc: value.core_ideas,
                                    keyconcept_id: value.keyconcepts_id,
                                    kc_name: value.kc_name,
                                    display: false,
                                };
                            }
                            core_ideas[ key ] = core_ideasobj;
                            var vocabularyobj = {
                                id: "",
                                name: "",
                                desc: "",
                                keyconcept_id: "",
                                kc_name: "",
                                display: true,
                            };
                            if ( value.academic_vocabulary != "" ) {
                                //Assign flag for empty records
                                vocabulary_flag = true;
                                vocabularyobj = {
                                    id: value.id,
                                    name: value.name,
                                    desc: value.academic_vocabulary,
                                    keyconcept_id: value.keyconcepts_id,
                                    kc_name: value.kc_name,
                                    display: false,
                                };
                            }
                            vocabulary[ key ] = vocabularyobj;
                        } );
                        $scope.summaries = summary;
                        $scope.standardarr = standards;
                        $scope.essential_questions = essential_questions;
                        $scope.core_ideas = core_ideas;
                        $scope.vocabularies = vocabulary;
                        $scope.lesson_sequence = lesson_sequence;
                        $scope.loadresult = false;
                        //Assign flag for empty records
                        $scope.summary_flag = summary_flag;
                        $scope.standardarr_flag = standards_flag;
                        $scope.essential_questions_flag = essential_questions_flag;
                        $scope.core_ideas_flag = core_ideas_flag;
                        $scope.vocabulary_flag = vocabulary_flag;
                        $scope.lesson_sequence_flag = lesson_sequence_flag;
                    } else {
                        $scope.summaries = "";
                        $scope.standardarr = "";
                        $scope.essential_questions = "";
                        $scope.core_ideas = "";
                        $scope.vocabularies = "";
                        $scope.lesson_sequence = "";
                        $scope.loadresult = false;
                        //Assign flag for empty records
                        $scope.summary_flag = false;
                        $scope.standardarr_flag = false;
                        $scope.essential_questions_flag = false;
                        $scope.core_ideas_flag = false;
                        $scope.vocabulary_flag = false;
                        $scope.lesson_sequence_flag = false;
                    }
                } )
                .error( function ( response ) {
                    $scope.loadresult = false;
                } );
        };

        /* Load on init, list of all keyconecpets */

        $scope.getLessonDetail = function ( form ) {
            var serviceBase = config.url;
            var last_lession_id = $cookieStore.get( "last_lession_id" );

            var Obj = {
                lession_id: last_lession_id,
            };
            $http
                .post( serviceBase + "lesson/getLessonDetails", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        var lessondata = angular.fromJson( response.DATA );

                        $scope.lesson_data = lessondata[ 0 ];
                        $scope.lesson_data.last_lession_id = last_lession_id[ "last_id" ];
                    } else {
                        $scope.lesson_data = "";
                    }
                } )
                .error( function ( response ) {
                    $scope.lesson_data = "";
                } );
        };

        $scope.checkItem = function ( id ) {
            var checked = false;
            var checkboxes = localStorageService.get( "key_concepts" );
            if ( angular.isArray( checkboxes ) ) {
                for ( var i = 0; i <= checkboxes.length; i++ ) {
                    if ( id == checkboxes[ i ] ) {
                        checked = true;
                    }
                }
            }
            return checked;
        };

        // Check at render page
        $scope.checkWebbItem = function ( id ) {
            var checked = false;
            var checkboxes = localStorageService.get( "webbs" );

            if ( angular.isArray( checkboxes ) ) {
                for ( var i = 0; i <= checkboxes.length; i++ ) {
                    if ( id == checkboxes[ i ] ) {
                        checked = true;
                    }
                }
            }
            return checked;
        };

        $scope.checkLearningItem = function ( id ) {
            var checked = false;
            var checkboxes = localStorageService.get( "Learning_target" );
            //console.log(checkboxes);
            if ( angular.isArray( checkboxes ) ) {
                for ( var i = 0; i <= checkboxes.length; i++ ) {
                    if ( id == checkboxes[ i ] ) {
                        checked = true;
                    }
                }
            }
            return checked;
        };

        $scope.addSummary = function () {
            var summarybox = [];
            var summary_storage = [];
            angular.forEach( $scope.summaries, function ( value, key ) {
                //console.log(key);
                summarybox.push( value );
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                summary_storage.push( temp_array );
            } );

            /* Save in database */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.summarybox = summarybox;
            $scope.smbxdiv = true;
            $scope.emptybxdiv = false;
        };
        $scope.addStandards = function () {
            var standardbox = [];
            var standrad_storage = [];
            angular.forEach( $scope.standardarr, function ( value, key ) {
                //console.log(key);
                standardbox.push( value );
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                standrad_storage.push( temp_array );
                console.log( "standrad_storage: ", standrad_storage );
            } );

            /* Save in database */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );

            $scope.standardbox = standardbox;
            $scope.stbxdiv = true;
            $scope.emptybxdiv = false;
        };

        $scope.addEssential = function () {
            var essentialbox = [];
            var essential_storage = [];
            angular.forEach( $scope.essential_questions, function ( value, key ) {
                //console.log(key);
                essentialbox.push( value );
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                essential_storage.push( temp_array );
            } );

            /* Save in database */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );

            $scope.essentialbox = essentialbox;
            $scope.enbxdiv = true;
            $scope.emptybxdiv = false;
        };
        $scope.addCoreIdeas = function () {
            var core_ideasbox = [];
            var coreideas_storage = [];
            angular.forEach( $scope.core_ideas, function ( value, key ) {
                //console.log(key);
                core_ideasbox.push( value );
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                coreideas_storage.push( temp_array );
            } );

            /* Save in database */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.core_ideasbox = core_ideasbox;
            $scope.cibxdiv = true;
            $scope.emptybxdiv = false;
        };

        $scope.addVocabulary = function () {
            var vocabulariesbox = [];
            var vocabulary_storage = [];
            angular.forEach( $scope.vocabularies, function ( value, key ) {
                //console.log(key);
                vocabulariesbox.push( value );
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                vocabulary_storage.push( temp_array );
            } );

            /* Save in database  */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.vocabulariesbox = vocabulariesbox;
            $scope.vbbxdiv = true;
            $scope.emptybxdiv = false;
        };

        $scope.addLessonSequence = function () {
            var lessonsequencebox = [];
            var sequence_storage = [];
            angular.forEach( $scope.sequence, function ( value, key ) {
                //console.log(key);
                lessonsequencebox.push( value );
                let temp_array = {
                    id: value.id,
                    name: value.definition,
                };
                sequence_storage.push( temp_array );
            } );

            /* Save in database  */
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.lessonsequencebox = lessonsequencebox;
            $scope.vbbxdiv = true;
            $scope.emptybxdiv = false;
        };

        $scope.addSummaryEditor = function () {
            $scope.editor_summary = "";
            var html_summary = "";
            var summary_storage = [];

            angular.forEach( $scope.summaries, function ( value, key ) {
                if ( value.desc == "" ) {
                    html_summary += "";
                } else {
                    html_summary +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.name +
                        "</strong>" +
                        "<ul><li>" +
                        value.desc +
                        "</li></ul>" +
                        "</div>";
                }

                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                summary_storage.push( temp_array );
            } );

            /* Save in database */

            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                summary_storage: summary_storage,
            };

            $scope.obJsummaryCheck = {
                summary_storage: summary_storage,
            };
            localStorageService.set( "obJsummaryCheck", $scope.obJsummaryCheck );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/savesummary", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {} else {}
                } )
                .error( function ( response ) {} );

            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.editor_summary = html_summary;
            $scope.emptybxdiv = false;
            $scope.modification( "flag" );
        };
        $scope.addStandardsEditor = function () {
            $scope.editor_standards = "";
            var html_standard = "";
            var standrad_storage = [];
            angular.forEach( $scope.standardarr, function ( value, key ) {
                //console.log(value);
                /**
                 * TODO:
                 */
                if ( value.desc == "" ) {
                    html_standard += "";
                } else {
                    html_standard +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.name +
                        "</strong>" +
                        "<ul><li>" +
                        value.desc +
                        "</li></ul>" +
                        "</div>";
                }
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                standrad_storage.push( temp_array );
            } );

            /* Save in database */

            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                standrad_storage: standrad_storage,
            };
            $scope.obJstandradCheck = {
                standrad_storage: standrad_storage,
            };
            localStorageService.set( "obJstandradCheck", $scope.obJstandradCheck );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/savestandrad", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {} else {}
                } )
                .error( function ( response ) {} );
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.editor_standards = html_standard;
            $scope.emptybxdiv = false;
            $scope.modification( "flag" );
        };

        $scope.addEssentialEditor = function () {
            $scope.editor_essential_questions = "";
            var html_essential = "";
            var essential_storage = [];
            angular.forEach( $scope.essential_questions, function ( value, key ) {
                //console.log(value);

                if ( value.desc == "" ) {
                    html_essential += "";
                } else {
                    html_essential +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.name +
                        "</strong>" +
                        "<ul><li>" +
                        value.desc +
                        "</li></ul>" +
                        "</div>";
                }

                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                essential_storage.push( temp_array );
            } );
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.editor_essential_questions = html_essential;
            $scope.emptybxdiv = false;

            /**NOTE: save in database */
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                essential_storage: essential_storage,
            };
            $scope.obJessentialCheck = {
                essential_storage: essential_storage,
            };
            localStorageService.set( "obJessentialCheck", $scope.obJessentialCheck );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/saveessential", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {} else {}
                } )
                .error( function ( response ) {} );
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.editor_essential = html_essential;
            $scope.emptybxdiv = false;
            $scope.modification( "flag" );
        };

        $scope.addCoreIdeasEditor = function () {
            $scope.editor_core_ideas = "";
            var html_core_ideas = "";
            var coreideas_storage = [];
            angular.forEach( $scope.core_ideas, function ( value, key ) {
                if ( value.desc == "" ) {
                    html_core_ideas += "";
                } else {
                    html_core_ideas +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.name +
                        "</strong>" +
                        "<ul><li>" +
                        value.desc +
                        "</li></ul>" +
                        "</div>";
                }
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                coreideas_storage.push( temp_array );
            } );

            /* Save in database */
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                coreideas_storage: coreideas_storage,
            };
            $scope.obJcoreideasCheck = {
                coreideas_storage: coreideas_storage,
            };
            localStorageService.set( "obJcoreideasCheck", $scope.obJcoreideasCheck );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/savecoreideas", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {} else {}
                } )
                .error( function ( response ) {} );

            $scope.emptybxdiv = false;
            $scope.editor_core_ideas = html_core_ideas;
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.modification( "flag" );
        };

        $scope.addVocabularyEditor = function () {
            $scope.editor_vocabulary = "";
            var html_vocabulary = "";
            var vocabulary_storage = [];
            angular.forEach( $scope.vocabularies, function ( value, key ) {
                if ( value.desc == "" ) {
                    html_vocabulary += "";
                } else {
                    html_vocabulary +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.name +
                        "</strong>" +
                        "<ul><li>" +
                        value.desc +
                        "</li></ul>" +
                        "</div>";
                }
                var temp_array = {
                    target_id: value.id,
                    keyconcept_id: value.keyconcept_id,
                };
                vocabulary_storage.push( temp_array );
            } );

            /* Save in database */
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var Obj = {
                lession_id: last_lession_id.last_id,
                vocabulary_storage: vocabulary_storage,
            };
            $scope.obJvocabularyCheck = {
                vocabulary_storage: vocabulary_storage,
            };
            localStorageService.set( "obJvocabularyCheck", $scope.obJvocabularyCheck );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "lesson/savevocabulary", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {} else {}
                } )
                .error( function ( response ) {} );
            $scope.emptybxdiv = false;
            $scope.disblelink = "enable";
            localStorageService.set( "disblelink", "enable" );
            $scope.editor_vocabulary = html_vocabulary;
            $scope.modification( "flag" );
        };

        $scope.addLessonSequenceEditor = function () {
            $scope.editor_lesson_sequence = "";
            let html_sequence = "";
            let sequence_storage = [];
            console.log( "Sequence: ", sequence );
            sequence.forEach( function ( value, key ) {
                if ( value.definition === "" ) {
                    html_sequence += "";
                } else {
                    html_sequence +=
                        '<div class="result-discription">' +
                        "<strong>" +
                        value.definition +
                        "</strong>" +
                        "<ul><li>" +
                        value.name +
                        "</li></ul>" +
                        "</div>";
                }

                let temp_array = {
                    id: value.id,
                    name: value.definition,
                };
                sequence_storage.push( temp_array );
            } );

            /* Save in database */
            var lession_id = $cookieStore.get( "last_lession_id" );
            lession_id = angular.fromJson( lession_id.last_id );

            let Obj = {
                lession_id: lession_id,
                sequence_storage: sequence,
            };

            $scope.obJsequenceCheck = {
                sequence_storage: Obj,
            };
            console.log( "Obj :", Obj );

            localStorageService.set( "obJsequenceCheck", Obj );

            console.log( "last_lession_id: ", lession_id );
            var user_id = Auth.getUserId();
            console.log( "user_id: ", user_id );
            localStorage.setItem( "sequence", JSON.stringify( sequence ) );
            sequence_storage.push( sequence );
            console.log( "sequence_storage: ", sequence_storage );
            console.log( "sequence length: ", sequence.length );
            console.log( "sequence: ", sequence );

            $scope.disablelink = "enable";
            localStorageService.set( "disablelink", "enable" );
            $scope.editor_lesson_sequence = html_sequence;
            $scope.emptybxdiv = false;
            // $scope.modification('flag');
        };

        /* This function will load summary, vocabulary, core ideas */
        $scope.getLessonFixedcontent = function () {
            cfpLoadingBar.start();

            var checkboxes = localStorageService.get( "Learning_target" );
            if ( checkboxes == undefined ) {
                checkboxes = "";
            }

            if ( checkboxes.length > 0 ) {
                $scope.emptybxdiv = false;
            } else {
                $scope.emptybxdiv = true;
            }
            localStorageService.remove( "lesson_modification" );
            var last_lession_id = $cookieStore.get( "last_lession_id" );
            var learning_targets = localStorageService.get( "Learning_target" );

            if ( learning_targets == null ) {
                $scope.classdiv = true;
                return false;
            } else {
                var Obj = {
                    last_lession_id: last_lession_id.last_id,
                    targets: learning_targets,
                };
                var serviceBase = config.url;
                $http
                    .post( serviceBase + "learningtarget/getEditorContent", Obj )
                    .success( function ( response ) {
                        $scope.classdiv = true;
                        if ( response.STATUS == 1 ) {
                            console.log( response.DATA );
                            $scope.editor_summary = response.DATA.summary;
                            $scope.editor_standards = response.DATA.standards;
                            $scope.editor_essential_questions =
                                response.DATA.essential_questions;
                            $scope.editor_core_ideas = response.DATA.core_ideas;
                            $scope.editor_vocabulary = response.DATA.vocabulary;
                            $scope.editor_lesson_sequence = response.DATA.lesson_sequence;
                        } else {}
                        $scope.disblelink = "disable-link";
                        localStorageService.set( "disblelink", "disable-link" );
                    } )
                    .error( function ( response ) {
                        $scope.classdiv = true;
                        $scope.disblelink = "disable-link";
                        localStorageService.set( "disblelink", "disable-link" );
                    } );
            }
        };

        /* This function will load summary, vocabulary, core ideas  in modification tab */
        $scope.getEditorcontent = function () {
        $scope.modi_re = true;
        $scope.classdiv = false;
        cfpLoadingBar.start();

            newFunction_1( localStorageService, $scope );

            var local_content = localStorageService.get( "lesson_modification" ) || "";
            if ( local_content != "" ) {
                $scope.lesson_modification = local_content;
                $scope.classdiv = true;
            } else {
                var last_lession_id = $cookieStore.get( "last_lession_id" );
                var learning_targets = localStorageService.get( "Learning_target" );
                var Obj = {
                    last_lession_id: last_lession_id.last_id,
                };

                var serviceBase = config.url;
                $http
                    .post( serviceBase + "learningtarget/modification", Obj )
                    .success( function ( response ) {
                        $scope.classdiv = true;
                        if ( response.STATUS == 1 ) {
                            if ( response.DATA.content != "" ) {
                                $scope.lesson_modification = response.DATA.content;
                            } else {
                                $scope.lesson_modification =
                                    response.DATA.summary +
                                    response.DATA.standards +
                                    response.DATA.essential_questions +
                                    response.DATA.core_ideas +
                                    response.DATA.vocabulary +
                                    response.DATA.lesson_sequence;
                            }
                        } else {}
                    } );
            }
        };
        $scope.getLessonDetail();

        $scope.trustAsHtml = function ( html ) {
            return $sce.trustAsHtml( html );
        };

        $scope.getdownloadPdfData = function () {
            let i = 1;
            newFunction( i );
            console.trace;
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( "last_lession_id" );
            lession_id = angular.fromJson( lession_id );
            var Obj = newFunction( lession_id, user_id, $scope );
            var serviceBase = config.url;
            $http
                .post( serviceBase + "LessonUser/saveTempPdfData", Obj )
                .success( function ( response ) {
                    if ( response.STATUS == 1 ) {
                        Flash.create( "success", response.MESSAGE );
                    } else {
                        Flash.create( "danger", response.MESSAGE );
                    }
                } )
                .error( function ( response ) {} );
        };
    },
] );

function newFunction( i ) {
    console.log( "Starting getDownPdfData" );
    console.info( "Info message" );
    console.warn( "Warning message" );
    console.error( "Error message" );
    console.assert( i == 0, "I is not 0" );
    console.dir( document.body );
}

function newFunction_1( localStorageService, $scope ) {
    var checkboxes = localStorageService.get( "Learning_target" );
    if ( checkboxes == undefined ) {
        checkboxes = "";
    }
    if ( checkboxes.length > 0 ) {
        $scope.emptybxdiv = false;
    } else {
        $scope.emptybxdiv = true;
    }
}

function newFunction( lession_id, user_id, $scope ) {
    return {
        lesson_id: lession_id.last_id,
        user_id: user_id,
        summary: $scope.editor_summary,
        standards: $scope.editor_standards,
        essential_questions: $scope.editor_essential_questions,
        core_ideas: $scope.editor_core_ideas,
        vocabulary: $scope.editor_vocabulary,
        lesson_sequence: $scope.editor_lesson_sequence,
    }
}

//?
//!
//todo

/**TODO: */
/**FIXME: */
/**NOTE: */
/**DEBUG:  */