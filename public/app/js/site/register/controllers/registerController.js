var registerPageApp = angular.module('registerPageModule');
 
registerPageApp.controller('registerController', ['config','$scope','$location','Auth', '$http', '$routeParams','Flash','localStorageService',

	function( config,$scope,$location,Auth, $http, $routeParams, Flash, localStorageService, flash) { 
		$scope.user = {};
	    $scope.errors = {};
	    $scope.authenticatedUser = {};
	    $scope.authFormToken = {};
	    $scope.resetToken = {};
	    $scope.flash = flash;
	    $scope.message = {}; 
		$scope.loading = false;
		$scope.prodObj  = [];

			/* Site Registration */
	    $scope.register = function(form) {
 
	      $scope.submitted = true;
	 
	        Auth.register({
	          firstname: $scope.firstname,
	          lastname: $scope.lastname,
	          email: $scope.email, 
	          password: $scope.password, 
	          confirm_password: $scope.confirmPassword,
	          country: $scope.country,
	          phone_number: $scope.phone_number,
	          usertype: $scope.usertype 
	        })
	        .then( function(response) { 
	          // Logged in, redirect to home
	          if(response.STATUS == 1) {
	            $scope.authenticatedUser = response; 

	            // Flash.create('success', response.MESSAGE);
	            localStorageService.set("email_verify", '1');
	            //Flash.setMessage(response.MESSAGE,"Success");
	            $location.path('/login');
	          } else { 
	            Flash.create('danger', response.MESSAGE);
	            //$scope.getMessage = {"message": response.MESSAGE, "type": "Error"};
	          }
	        })
	        // .catch( function(err) { 
	        //   $scope.errors.other = err.message;
	        // });
	    };

	    $scope.getAllcountry = function(form) {				
			var serviceBase = config.url;
				$http.post(serviceBase+'country/getAllCountries').success(function(response) {
				if(response.STATUS == 1){ 
					 $scope.countries = response.DATA;

				}else{
					$scope.countries = "";
				}

			}).error(function(response) {

		    	$scope.countries = "";
		    });
	      
	    };

	     
	}
]);



	
