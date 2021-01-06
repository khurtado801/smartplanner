
var globalApp = angular.module('globalModule', ['ngCookies', "pascalprecht.translate"]);
globalApp.config(['$routeProvider','$httpProvider','$translateProvider', function ($routeProvider,$httpProvider,$translateProvider) {
    // add translation tables
    $translateProvider.translations('en', translationsEN);
    $translateProvider.translations('de', translationsDE);
    $translateProvider.preferredLanguage('de');
    // remember language
    $translateProvider.useLocalStorage();
}]);
