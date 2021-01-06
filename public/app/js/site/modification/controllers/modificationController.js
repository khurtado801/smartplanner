//var outlineModule = angular.module('myApps',['ui.tinymce']);
var ModificationModule = angular.module( 'ModificationPageModule' );

ModificationModule.controller( 'modificationController', [ '$scope', '$http', 'config', 'Flash', '$cookieStore', '$window', 'localStorageService', '$location', '$uibModal', '$sce', 'Auth', 'cfpLoadingBar',
    function ( $scope, $http, config, Flash, $cookieStore, $window, localStorageService, $location, $uibModal, $sce, Auth, cfpLoadingBar ) {

        $scope.classdiv = false;

        $scope.saveOutline = function () {

            var keyconcept_id = localStorageService.get( "key_concepts" );
            var learningtarget_id = localStorageService.get( "Learning_target" );
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( 'last_lession_id' );
            lession_id = angular.fromJson( lession_id );

            var summary_box = localStorageService.get( "summary_box" );
            if ( learningtarget_id.length == 0 || keyconcept_id.length == 0 ) {
                $scope.editor_summary = "";
                $scope.editor_standards = "";
                $scope.editor_essential_questions = "";
                $scope.editor_core_ideas = "";
            }

            //console.log($scope);
            var Obj = {
                "keyconcept_id": keyconcept_id,
                "learningtarget_id": learningtarget_id,
                "lesson_id": lession_id.last_id,
                "user_id": user_id,
                "summary": $scope.editor_summary,
                "standards": $scope.editor_standards,
                "essential_questions": $scope.editor_essential_questions,
                "core_ideas": $scope.editor_core_ideas,
                "vocabulary": $scope.editor_vocabulary,
                'summary_box': summary_box
            };

            var serviceBase = config.url;
            $http.post( serviceBase + 'LessonUser/createLessonByUser', Obj ).success( function ( response ) {
                if ( response.STATUS == 1 ) {
                    $scope.disblelink = "disable-link";
                    localStorageService.set( "disblelink", "disable-link" );
                    Flash.create( 'success', response.MESSAGE );
                } else {
                    Flash.create( 'danger', response.MESSAGE );
                }

            } ).error( function ( response ) {


            } );
        };

        $scope.saveModifacation = function () {
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( 'last_lession_id' );
            lession_id = angular.fromJson( lession_id );

            //console.log($scope);
            var Obj = {
                "lesson_id": lession_id.last_id,
                "user_id": user_id,
                "content": $scope.lesson_modification
            };

            var serviceBase = config.url;
            $http.post( serviceBase + 'LessonUser/savemodification', Obj ).success( function ( response ) {
                if ( response.STATUS == 1 ) {
                    $scope.disblelink = "disable-link";
                    localStorageService.set( "disblelink", "disable-link" );
                    Flash.create( 'success', response.MESSAGE );
                } else {
                    Flash.create( 'danger', response.MESSAGE );
                }

            } ).error( function ( response ) {

            } );
        };

        $scope.setPreview = function () {
            var local_store = localStorageService.get( "lesson_modification" ) || '';
            if ( local_store == "" && !$scope.lesson_modification ) {
                localStorageService.set( "editor_summary", $scope.editor_summary );
                localStorageService.set( "editor_standards", $scope.editor_standards );
                localStorageService.set( "editor_essential_questions", $scope.editor_essential_questions );
                localStorageService.set( "editor_core_ideas", $scope.editor_core_ideas );
                localStorageService.set( "editor_vocabulary", $scope.editor_vocabulary );
            } else {
                if ( $scope.lesson_modification ) {
                    localStorageService.set( "lesson_modification", $scope.lesson_modification );
                }
            }

            $location.path( '/preview/create' );
            $scope.checkurl_preview();
        };

        $scope.trustAsHtml = function ( html ) {
            return $sce.trustAsHtml( html );
        };

        $scope.getdownloadPdfData = function () {
            debugger;
            var user_id = Auth.getUserId();
            var lession_id = $cookieStore.get( 'last_lession_id' );
            lession_id = angular.fromJson( lession_id );

            var Obj = {
                "lesson_id": lession_id.last_id,
                "user_id": user_id,
                "summary": $scope.editor_summary,
                "standards": $scope.editor_standards,
                "essential_questions": $scope.editor_essential_questions,
                "core_ideas": $scope.editor_core_ideas,
                "vocabulary": $scope.editor_vocabulary
            };

            var serviceBase = config.url;
            $http.post( serviceBase + 'LessonUser/saveTempPdfData', Obj ).success( function ( response ) {
                if ( response.STATUS == 1 ) {
                    Flash.create( 'success', response.MESSAGE );
                } else {
                    Flash.create( 'danger', response.MESSAGE );
                }
            } ).error( function ( response ) {} );
        };

        $scope.checkurl = function () {
            var path = $location.path();
            if ( path === "/modification/create" ) {
                return true;
            }
            return false;
        }
        //flg
        $scope.redirect = function ( redirectpath ) {
            if ( redirectpath == 'modification/create' || redirectpath == 'outline/create' ) {
                $scope.classdiv = false;
            }
            var link = localStorageService.get( "disblelink" );
            if ( link == "enable" && $scope.disblelink == "enable" ) {
                if ( confirm( 'Are you sure you want to leave page without saving your changes?' ) ) {
                    $location.path( redirectpath );
                }
            } else {
                $location.path( redirectpath );
            }
            return false;
        }
    }
] );

ModificationModule.filter( 'htmlToPlaintext', function () {
    return function ( text ) {
        return text ? String( text ).replace( /<[^>]+>/gm, '' ) : '';
    };
} );