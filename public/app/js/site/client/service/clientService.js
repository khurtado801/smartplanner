'use strict';

angular.module('clientModule')
  .factory('Client', function Client(config,$location, $rootScope, $http, $cookieStore, $q,$localStorage,$window) {
     
    var serviceBase = config.url;
    var is_currentUser = ""; 
    var user;
    var profile_files = {};
    
    return {  
  
      profile_pic: function(user) {
            var profile_formData = new FormData();
            angular.forEach(user.profile_files,function(obj){
                profile_formData.append('profile_file[]', obj.lfFile);
            });  
            // Code for profile pic upload 
            if(profile_files!=null && profile_files!=""){ 
              $http.post(serviceBase+'client/profile_upload', profile_formData, {
                  transformRequest: angular.identity,
                  headers: {'Content-Type': undefined}
              }).success(function(response) { 
                  return profile_image = angular.fromJson(response.RESULT); 
              }).error(function(response) { 
                  Flash.create('danger', response.MESSAGE);
              });
            }
      }

    };
  });
