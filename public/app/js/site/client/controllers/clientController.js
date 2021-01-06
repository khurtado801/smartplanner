

var app = angular.module('fileUpload', ['ngFileUpload']);
app.controller('MyCtrl', ['$scope', 'Upload', '$timeout','config', function($scope, Upload, $timeout,config) {
    $scope.uploadPic = function(user,file) {
    	//var profile_file = $scope.profile_files;
    	var serviceBase = config.url; 
		var uploadUrl = serviceBase+'client/profile_upload';
	    file.upload = Upload.upload({
	      url: uploadUrl,
	      data: { user:$scope.user, file: file},
	    });

	    file.upload.then(function (response) {
	      $timeout(function () {
	        file.result = response.data;
	      });
	    }, function (response) {
	      if (response.status > 0)
	        $scope.errorMsg = response.status + ': ' + response.data;
	    }, function (evt) {
	      // Math.min is to fix IE which reports 200% sometimes
	      file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
	    });
	}
}]);

var clientApp = angular.module('clientModule');
clientApp.controller('clientController', ['Upload','Flash','$filter','$mdpDatePicker','$mdpTimePicker','config','$cookieStore','Auth','$timeout', '$scope', '$location', '$http', '$routeParams','$localStorage','$route', 
	
	function(Upload,Flash,$filter,$mdpDatePicker,$mdpTimePicker,config,$cookieStore,Auth,$timeout, $scope, $location, $http, $routeParams, $localStorage,$route) {
		//$scope.currentUser = [];
		$scope.user = {}; 
		var serviceBase = config.url; 
		if(Auth.getSessionCurrentUser()){
			$scope.currentUser = Auth.getSessionCurrentUser();
			var currentUser = angular.fromJson($scope.currentUser);  
	    	var user_id =  currentUser[0].id;
	    	$scope.user.user_id =  currentUser[0].id;  
		}else{
			Auth.logout();
		}
		$scope.myVar = true;


		
		//$scope.user = user_id;
		$scope.getClientData = function () {  
			 
	    	var Obj = {'id':user_id};
	    	
			$http.post(serviceBase+'client/clientData', Obj).success(function(response) {
				if(response.STATUS == 1){
					 $scope.user = response.RESULT;
					  //console.log($scope.user); 
					  	$scope.getSelectedStates(response.RESULT.countryId); 
		    			$scope.getSelectedCities(response.RESULT.stateId); 
		    			 
						$scope.user.birth_date = new Date(response.RESULT.birth_date);
						$scope.user.gender = response.RESULT.gender;
						$scope.user.portfolio_images = angular.fromJson(response.RESULT.portfolio_images);
						$scope.user.profile_image = angular.fromJson(response.RESULT.profile_image); 
						/* var newObj = {};
						 $scope.user.profile_image = angular.extend(newObj, $scope.user.profile_image); */
						//console.log($scope.user.profile_image);
						//console.log($scope.user.portfolio_images);
					      //$scope.user.birth_date = $filter('date')(response.RESULT.birth_date, "yyyy-MM-dd");  
				}else{
					$scope.user = "";
				}

			}).error(function(response) {

		    	$scope.user = "";
		    });
		    

	    }; 

	     

		$scope.project = {
			job_title:"job Title",
			job_subtitle:"job_subtitle",
			job_description:"job_description",
			job_comments:"job_comments",
			job_cost_min:"0",
			job_cost_max:"1000",
			job_keywords:"PHP, Angular",
			job_title:"New Job",
			
		};

		$scope.genders = ('Male'+'Female'+'Other').split(' ').map(function(gender) {
			//console.log(gender);
        	return {abbrev: gender};
     	});

     	$scope.usersgenders = [ { id: 'Male', name: 'Male',selected:false }, { id: 'Female', name: 'Female',selected:false },  
     	 						{ id: 'Other', name: 'Other',selected:false } ];
		//$scope.selectedUser = { id: 'Male', name: 'Male',selected:true };

     	$scope.qualifications = ('Below-Graduate '+'Graduate '+'Above-Graduate').split(' ').map(function(qualification) {
        	return {abbrev: qualification};
     	});
     	$scope.jobsAvlFor = ('Freelancer '+'Company '+'Freelancer/Company').split(' ').map(function(jobAvlFor) {
        	return {abbrev: jobAvlFor};
     	});

     	$scope.jobStage = ('Starting '+'Pending '+'Processing '+'Finished').split(' ').map(function(jobAvlFor) {
        	return {abbrev: jobAvlFor};
     	});
     
     	$scope.tooltip = { direction : 'center', delay:500};

      	
     	//console.log(Auth.getCurrentUser());
	    $scope.getClientUpdateData = function () {  
	    	var Obj = {};
	    	
			$http.post(serviceBase+'country/getCountries', Obj).success(function(response) {
				if(response.STATUS == 1){ 
					 $scope.countries = response.DATA;
				}else{
					$scope.countries = "";
				}

			}).error(function(response) {

		    	$scope.countries = "";
		    });
		    
	    };

	 
	  
    	 
	    $scope.getAllStates = function (countryId) {	
	    	 
	    if(countryId!=""){ var countryId = countryId;}else{var countryId = $scope.user.countryId; }

	    	var Obj = {"countryId":countryId};
			$http.post(serviceBase+'country/getStates',Obj).success(function(response) {
				if(response.STATUS == 1){
					$scope.states = response.DATA;
				}else{
					$scope.states = "";
				}
			}).error(function(response) {
		    	$scope.states = "";
		    });
		};

	    $scope.getSelectedStates = function (countryId) {	
	    	 
	    	var Obj = {"countryId":countryId};
			$http.post(serviceBase+'country/getStates',Obj).success(function(response) {
				if(response.STATUS == 1){
					$scope.states = response.DATA;
				}else{
					$scope.states = "";
				}
			}).error(function(response) {
		    	$scope.states = "";
		    });
		};

		$scope.getSelectedCities = function (stateId) {	
		 
	    	var Obj = {"stateId":stateId};
			$http.post(serviceBase+'country/getCities',Obj).success(function(response) {
				if(response.STATUS == 1){
					$scope.cities = response.DATA;
				}else{
					$scope.cities = "";
				}
			}).error(function(response) {
		    	$scope.cities = "";
		    });
		};
	    
	 
	    // upload on file select or drop
	  

	    $scope.postProfile_pic = function(user){
	    	 var profile_formData = new FormData();
		            angular.forEach($scope.profile_files,function(obj){
		                profile_formData.append('profile_file[]', obj.lfFile);
		            }); 
            // Code for profile pic upload 
            if($scope.profile_files!=null && $scope.profile_files!=""){ 
	            $http.post(serviceBase+'client/profile_upload', profile_formData, {
	                transformRequest: angular.identity,
	                headers: {'Content-Type': undefined}
	            }).success(function(response) { 
	            	$scope.user.profile_image = angular.fromJson(response.RESULT); 
	            }).error(function(response) { 
	            	Flash.create('danger', response.MESSAGE);
			    });
	        }
	        //console.log(profile_formData);return false;
	    }; 
	   

	    $scope.submit = function() {
	      if ($scope.profile_files) {
	        $scope.upload($scope.profile_files);
	      }
	    };

	    $scope.upload = function (user,picFile) { 
            Upload.upload({
                url: serviceBase+'client/profile_upload',
                data: { user:$scope.user, file: picFile}
            }).progress(function (evt) {
                var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                $scope.log = 'progress: ';
            }).success(function (data) {
                
            });         
	            
	    };

	   /* // upload on file select or drop
	    $scope.upload = function (file) {
 
			var uploadUrl = serviceBase+'client/profile_upload';
	        Upload.upload({
	            url: uploadUrl,
	            data: {file: file, 'username': $scope.user}
	        }).then(function (resp) {
	            console.log('Success ' + resp + 'uploaded. Response: ' + resp);
	        }, function (resp) {
	            console.log('Error status: ' + resp);
	        }, function (evt) {
	            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
	            console.log('progress: ' + progressPercentage + '% ' + evt);
	        });
	    };*/

	    $scope.uploadProfile = function(user,file) {
	    	//var profile_file = $scope.profile_files;
	    	var serviceBase = config.url; 
			var uploadUrl = serviceBase+'client/updateClient';
			if(file!=null){
			    upload = Upload.upload({
			      url: uploadUrl,
			      data: { user:$scope.user, profile_file: file},
			    }); 
			}else{
				upload = $http.post( 
			       uploadUrl,
			       { user:$scope.user}
			     ); 
			}
		    upload.then(function (response) {
		      $timeout(function () {
		        var result = response.data.RESULT;
		        $location.path('/client/profile/update');
		      });
		    }, function (response) {
		      if (response.status > 0)
		        $scope.errorMsg = response.status + ': ' + response.data;
		    	$location.path('/client/profile/update');
		    }, function (evt) { 
		      var progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
		      	$location.path('/client/profile/update');
		    });
		};

	    $scope.userDetails = function (user,file) {

	    	var profile_file = $scope.profile_files;
			var uploadUrl = serviceBase+'client/profile_upload';
			var userid = $scope.user.user_id;
			 
	    	var formData = new FormData();
            angular.forEach($scope.files,function(obj){
                formData.append('file[]', obj.lfFile);
            }); 
			 
					 
         	// alert(JSON.stringify($scope.allUsers)); return false;
	    	// Code for Multiple files upload 
            if($scope.files!=null && $scope.files!="undefined" && $scope.files!=""){ 
            	
	            $http.post(serviceBase+'client/file_upload', formData, {
	                transformRequest: angular.identity,
	                headers: {'Content-Type': undefined}
	            }).then(function(response){ 
	            	$scope.user.portfolio_images = $scope.user.portfolio_images; 
	            	//$scope.user.portfolio_images = angular.fromJson(response.data.RESULT);
	            	var data = angular.fromJson(response.data.RESULT);	
	            	for (var i=0; i<data.length; i++){
					    $scope.user.portfolio_images.push(data[i]);
					}   
			           
	                var currentUser = angular.fromJson($scope.currentUser);  
			    	var userObj1 = $scope.user;	 
			    	var userObj2 = $scope.user.portfolio_images; 
			    	var mergedObject = angular.extend(userObj1, userObj2); 
			    	$scope.uploadProfile(user,file);
	       			return false; 
	            },function(err){
	                Flash.create('danger', response.MESSAGE);
	               // $location.path("/client/profile/update");  
	            });
	            

	        }else{    
	        		if(file!=null){
		        		$scope.uploadProfile(user,file);
		        	}else{
		        		$scope.uploadProfile(user);
		        	}
	        		return false;  
	        }
	        
           // console.log($scope.user.file_result); return false;
	    	//$scope.upload($scope.user.files); 
	     	
	    };  

	    $scope.hideMe = function (portfolio_img) {
	        portfolio_img.hidden=true;
	        alert('hide this li');
	       // $location.path(serviceBase+"/client/profile/update"); 
	    }; 
		  

	    $scope.image_remove = function ($scope,user) {  
	    	$http.post(serviceBase+'client/remove_image',{
	          image_name: $scope,
	          user_id: user
	        }).success(function(response){ 
				if(response.STATUS == 1){
					$scope.categories = response.DATA;   
					 
					// $scope.splice($scope, 1);
				}else{
					$scope.categories = "";
				} 
			}).error(function(response) { 
		    	$scope.categories = "";
		    });
	    };

	    $scope.getAllCategories = function (stateId) { 
	    	var Obj = {}; 
			$http.post(serviceBase+'user/getAllCategories',Obj).success(function(response) { 
				if(response.STATUS == 1){
					$scope.categories = response.DATA;
				}else{
					$scope.categories = "";
				} 
			}).error(function(response) { 
		    	$scope.categories = "";
		    });
		};

	    $scope.getAllSkills = function (stateId) { 
	    	var Obj = {}; 
			$http.post(serviceBase+'user/getAllSkills',Obj).success(function(response) { 
				if(response.STATUS == 1){
					$scope.skills = response.DATA;
				}else{
					$scope.skills = "";
				} 
			}).error(function(response) { 
		    	$scope.skills = "";
		    });
		};

		/*Project Management*/
		$scope.getPostProjectData = function () { 
	    	$scope.getAllSkills();
	    	$scope.getAllCategories();
	    }

		$scope.postProject = function (project) { 
	    	var projectDataObj = $scope.project;  
			$http.post(serviceBase+'client/postProject',projectDataObj).success(function(response) { 
				if(response.STATUS == 1){
					$scope.postproject = response.DATA;
				}else{
					$scope.postproject = "";
				} 
			}).error(function(response) { 
		    	$scope.postproject = "";
		    });
		};

		$scope.model = {};
            $scope.files = [];
            $scope.uploadProgress = 0;
            var profile_file = $scope.profile_files;
			var uploadUrl = 'http://localhost/jobo/#/client/profile_upload';

            $scope.uploadFile = function (user) {
                var file = $scope.profile_files[0];
                $scope.upload = fileUpload.upload({
                    url: uploadUrl,
                    method: 'POST',
                    data: angular.toJson($scope.model),
                    file: file
                }).progress(function (evt) {
                    $scope.uploadProgress = parseInt(100.0 * evt.loaded / evt.total, 10);
                }).success(function (data) {
                    //do something
                });
            };

            $scope.onFileSelect = function ($files) {
                $scope.uploadProgress = 0;
                $scope.selectedFile = $files;
            };
        return false;






	}
]).filter('brDateFilter', function() {
    return function(dateSTR) {
        var o = dateSTR.replace(/-/g, "/"); // Replaces hyphens with slashes
        return Date.parse(o + " -0000"); // No TZ subtraction on this sample
    }
}).directive('progressBar', [
        function () {
            return {
                link: function ($scope, el, attrs) {
                    $scope.$watch(attrs.progressBar, function (newValue) {
                        el.css('width', newValue.toString() + '%');
                    });
                }
            };
        }
    ]);
 
/*
clientApp.service('sharedUsers', ['$http', function($http){
  	 
  	var allUsers = []; 

  	var getAll = function (profile_files,uploadUrl){

	var profile_formData = new FormData();
	angular.forEach(profile_files,function(obj){
		profile_formData.append('profile_file[]', obj.lfFile);
	}); 
	console.log(profile_formData); return false;
    $http.post(uploadUrl, profile_formData)
    .success(function(data) {        
       return allUsers = angular.fromJson(data);  
      });
    };

   return {
       getAll: getAll
   };
}]);

*/

 
