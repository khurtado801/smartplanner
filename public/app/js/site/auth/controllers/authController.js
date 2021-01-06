'use strict'; 

angular.module('authModule')
  .controller('authController', function (config,$scope,Flash,Auth,$location, $window, $routeParams,flash,$route,$cookieStore,localStorageService) {
      
    $scope.oldPassword = '';
    $scope.newPassword = '';    
    $scope.user = {};
    $scope.errors = {};
    $scope.authenticatedUser = {};
    $scope.authFormToken = {};
    $scope.resetToken = {};
    $scope.flash = flash;
    $scope.message = {};
    $scope.currentUserData = {};
    var currentUserData;
    var serviceBase = config.url;
    $scope.is_user = {}; 
    
    $scope.usertype='teacher';
 
    $scope.checkLogin = function() { 
        if (!Auth.isLoggedInUser()){ 
            return false;  
            //$location.path('/login');

        }else{  
            return true; 
        }   
         
    }; 
    
    $scope.select= function(current) {
      var path = $location.path();
      console.log(path);
      if(current===path){
        $scope.selectedclass = "active";
      }else{
        $scope.selectedclass = "";
      }
    };

    $scope.isUserLogin = function() {  
        return $scope.authenticatedUser = Auth.getCurrentUser();  
    };

    $scope.checkUser = function() {
       
      if($scope.authenticatedUser.error === true 
        && $scope.authenticatedUser.message === "Token Expired" || 
        $scope.authenticatedUser.tokenId === null){ 
          $location.path(serviceBase+"/register");
      } else {
        $scope.authenticatedUser = Auth.getCurrentUser();

        return $scope.authenticatedUser;  
      }
    };  
    /* get Form Token */
    $scope.getFormToken = function() {       
        return $scope.authFormToken = Auth.getFormToken();  
    };

    /* Site Login */
   

/* Site Registration */
   /* $scope.register = function(form) {
      
      $scope.submitted = true;
 
        Auth.register({
          firstname: $scope.firstname,
          lastname: $scope.lastname,
          email: $scope.email, 
          password: $scope.password,
          confirm_password: $scope.confirm_password,
          _token: $scope.authFormToken._token
        })
        .then( function(response) { 
          // Logged in, redirect to home
          if(response.STATUS == 1) {
            $scope.authenticatedUser = response; 

            Flash.create('success', response.MESSAGE);
            //Flash.setMessage(response.MESSAGE,"Success");
            $location.path('/login');
          } else { 
            Flash.create('danger', response.MESSAGE);
            //$scope.getMessage = {"message": response.MESSAGE, "type": "Error"};
          }
        })
        .catch( function(err) { 
          $scope.errors.other = err.message;
        });
    };*/

    // Action: Site user Forgot Password
    $scope.forgot = function(form) {

        Auth.forgot({
          mobile: $scope.mobile,
          _token: $scope.authFormToken._token
        })
        .then( function(response) {
          // Logged in, redirect to home
          if(response.error != true) {
            $scope.authenticatedUser = response;
            flash.setMessage(response.message,"Success");
            $location.path('/login');
          } else {
            $scope.getMessage = {"message": response.message, "type": "Error"};
          }
        })
        .catch( function(err) {
          $scope.errors.other = err.message;
        });
    }

    /* check reset Token */
    $scope.checkResetToken = function() {

        Auth.checkResetToken({
          'resettoken': $routeParams.token
        })
        .then( function(response) {
          
          if(response.error != true) {
            $scope.resetToken = {
              'token': $routeParams.token    
            };
          } else {
            flash.setMessage(response.message,"Error");
          }
        })
        .catch( function(err) {
          $scope.errors.other = err.message;
        });;
    };

    /* Reset Password - Submit - Admin */
    $scope.resetPassword = function(form) {
      $scope.submitted = true;
      //if(form.$valid) 
      // {
        Auth.resetPassword({
          password: $scope.password,
          resettoken: $scope.resetToken.token,
          _token: $scope.authFormToken._token
        })
        .then( function(response) {
          // Logged in, redirect to home
          if(response.error != true) {
            $scope.authenticatedUser = response;
            flash.setMessage(response.message,"Success");
            $location.url('/login');
          } else {
            flash.setMessage(response.message,"Error");
            $location.url('/login');
          }
        })
        .catch( function(err) {
          $scope.errors.other = err.message;
        });
      // }
    };

    $scope.loginOauth = function(provider) {
      $window.location.href = '/auth/' + provider;
    };

    /* Site Logout */
    $scope.logout = function() {
      localStorageService.clearAll();
      $cookieStore.remove('last_lession_id');
      Auth.logout();  
    }; 
}).directive("matcher", function($timeout) {
    return {
        restrict : "A",

        require : "ngModel",

        link : function(scope, element, attributes, ngModel) {

            ngModel.$validators.matcher = function(modelValue) {
                return attributes.matcher === modelValue;
            };
        }
    };
});
 
