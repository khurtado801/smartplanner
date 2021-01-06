angular.module('truckForLoad', [
    'ngRoute',
    'ngResource',
    'ngFlash',
    'ngResource',
    'ngCookies',
    'truckForLoadSite',
    'angular.filter',
]).config([
    '$routeProvider', '$locationProvider', '$httpProvider', 'cfpLoadingBarProvider',
    function ($routeProvider, $locationProvider, $httpProvider, cfpLoadingBarProvider) {

        $httpProvider.defaults.useXDomain = true;
        delete $httpProvider.defaults.headers.common['X-Requested-With'];

        $locationProvider.html5Mode(false).hashPrefix('');

        $httpProvider.interceptors.push(['$rootScope', '$q', '$cookieStore',
            function ($rootScope, $q, $cookieStore) {
                return {
                    request: function (config) {
                        config.headers = config.headers || {};
                        if ($cookieStore.get("tokenId")) {
                            config.headers.Authorization = 'Bearer ' + $cookieStore.get("tokenId");
                        }
                        return config;
                    },
                    response: function (res) {
                        if (res.status === 401) {
                            // Handle unauthenticated user.
                        }
                        return res || $q.when(res);
                    }
                };
            }
        ]);
    }
]).constant("config", {
    "url": "api/",
    "baseurl": "/",
    "siteurl": "/"
});
