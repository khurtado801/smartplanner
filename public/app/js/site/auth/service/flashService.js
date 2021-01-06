'use strict'; 
 
angular.module('truckForLoadSite')
  .factory('flash', function flash($location, Flash,$rootScope, $http, User, $cookieStore, $q,$timeout) {
  var queue = [];
  var currentMessage = "";
  $rootScope.flash = {};
  $rootScope.flash.timeout = 5000;
  $rootScope.hasFlash = false;  
   
  $rootScope.$on("$routeChangeSuccess", function() {
    currentMessage = queue.shift() || "";
  });

  return {  
    setMessage: function(message,type) {
         
      queue.push(
        {
          "message":message,
          "type":type
        }); 
     
    },
    getMessage: function() {

      return currentMessage;
    } 
  };
  });


// Demo controller
 

/*app.factory('Flash', ['$rootScope', '$timeout', function ($rootScope, $timeout) {
    var dataFactory = {};
    var counter = 0;
    dataFactory.setDefaultTimeout = function (timeout) {
        if (typeof timeout !== 'number') return;
        dataFactory.defaultTimeout = timeout;
    };

    dataFactory.defaultShowClose = true;
    dataFactory.setShowClose = function (value) {
        if (typeof value !== 'boolean') return;
        dataFactory.defaultShowClose = value;
    };
    dataFactory.setOnDismiss = function (callback) {
        if (typeof callback !== 'function') return;
        dataFactory.onDismiss = callback;
    };
    dataFactory.create = function (type, text, timeout, config, showClose) {
        var $this = undefined,
            flash = undefined;
        $this = this;
        flash = {
            type: type,
            text: text,
            config: config,
            id: counter++
        };
        flash.showClose = typeof showClose !== 'undefined' ? showClose : dataFactory.defaultShowClose;
        if (dataFactory.defaultTimeout && typeof timeout === 'undefined') {
            flash.timeout = dataFactory.defaultTimeout;
        } else if (timeout) {
            flash.timeout = timeout;
        }
        $rootScope.flashes.push(flash);
        if (flash.timeout) {
            flash.timeoutObj = $timeout(function () {
                $this.dismiss(flash.id);
            }, flash.timeout);
        }
        return flash.id;
    };
    dataFactory.pause = function (index) {
        if ($rootScope.flashes[index].timeoutObj) {
            $timeout.cancel($rootScope.flashes[index].timeoutObj);
        }
    };
    dataFactory.dismiss = function (id) {
        var index = findIndexById(id);
        if (index !== -1) {
            var flash = $rootScope.flashes[index];
            dataFactory.pause(index);
            $rootScope.flashes.splice(index, 1);
            $rootScope.$digest();
            if (typeof dataFactory.onDismiss === 'function') {
                dataFactory.onDismiss(flash);
            }
        }
    };
    dataFactory.clear = function () {
        while ($rootScope.flashes.length > 0) {
            dataFactory.dismiss($rootScope.flashes[0].id);
        }
    };
    dataFactory.reset = dataFactory.clear;
    function findIndexById(id) {
        return $rootScope.flashes.map(function(flash) {
            return flash.id;
        }).indexOf(id);
    }

    return dataFactory;
}]);*/
