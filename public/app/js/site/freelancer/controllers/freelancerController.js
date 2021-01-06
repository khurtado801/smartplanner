var freelancerApp = angular.module('freelancerModule');
freelancerApp.controller('freelancerController', ['Flash', 'Upload','$timeout', '$q', '$cookieStore', '$scope', '$location', '$http', '$routeParams','$localStorage', 
	
	function(Flash, Upload, $timeout, $q, $cookieStore, $scope, $location, $http, $routeParams, $localStorage) {
		$scope.loading = true;
		$scope.lions = false;
  		
		var skillsArray =[];
		$scope.user ={
			//skills:[3,7,10],
		
			/*firstname:"Amit",
			lastname:"Gupta",
			email:"amit@gmail.com",
			phone_number:"8306062028",
			street:"CG Road",
			zipcode:"38059",
			job_title:"My Job Title",
			profile_description:"profile description",
			invoice_address:"Invoice Address",
			delivery_address:"Delivery Address",
			gender:"Female",
			hourly_rate:"1000",
			website:"http://www.website.com",
			video:"http://www.video.com",
			sva_number:'SVA12345',
			skills:[3, 7],
			city:"39221",
			cityId:"39221",
			countryId:"212",*/

		};

		$scope.currentUser = angular.fromJson($cookieStore.get('userLogin'));
	   
	    if($scope.currentUser != null && $scope.currentUser != "undefiend" )
	    {
	    	$scope.user.userId = $scope.currentUser[0].id;
	    
	    } else {

	    	$location.path('/login');
	    }

		if($localStorage.allLocations && $localStorage.allLocations.length != null && $localStorage.allLocations.length > 0)
		{
			var allLocations = $localStorage.allLocations;

		} else {
			
			var allLocations = null;
		}

		$scope.tooltip = { direction : 'center', delay:500};
		$scope.genders = ('Male '+'Female '+'Other').split(' ').map(function(gender) {
        	return gender;
     	});
		$scope.cards = ('Visa '+'American Express '+'Discover').split(' ').map(function(card) {
        	return card;
     	});
     	$scope.ctypes = ('Single-Company '+'Collective-Society '+'Limited-Partnership').split(' ').map(function(ctype) {
        	return ctype;
     	});
	    

	    var self = this;
	    // list of `state` value/display objects
	    self.states        = loadAll();
	    self.selectedItem  = null;
	    self.searchText    = null;
	    self.querySearch   = querySearch;	    
	  	    function querySearch (query) {
	      var results = query ? self.states.filter( createFilterFor(query) ) : self.states;
	      var deferred = $q.defer();
	      $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
	      return deferred.promise;
	    }
	   
	    function loadAll() {
	      var allStates = allLocations;

	      return allStates.split(/, +/g).map( function (state) {
	        return {
	          value: state.toLowerCase(),
	          display: state
	        };
	      });
	    }

	    function createFilterFor(query) {
	      var lowercaseQuery = angular.lowercase(query);

	      return function filterFn(state) {
	        return (state.value.indexOf(lowercaseQuery) === 0);
	      };
	    }


	    $scope.getFreelanceUpdateData = function () {
	    	
	    	var Obj = {};

			$http.post('api/country/getCountries', Obj).success(function(response) {

				if(response.STATUS == 1)
				{
					 $scope.countries = response.DATA;
				}
				else
				{
					$scope.countries = "";
				}

			}).error(function(response) {

		    	$scope.countries = "";
		    });


		    $scope.getAllSkills();
		    $scope.getQualifications();
		    $scope.getFreelancerDetails($scope.user.userId);
	    };

	    $scope.getFreelancerDetails = function(id){

	    	if(id && id !=null && id != "undefiend")
	    	{
		    	var Obj = {"user_id":id};

		    	$http.post('api/freelancer/getFreelancerDetails', Obj).success(function(response) {
		    		//console.log(response);
					if(response.STATUS == 1)
					{
						 $scope.user = response.DATA;
					}
					else
					{
						$scope.user = "";
					}

				}).error(function(response) {
			    	//$scope.user = "";
			    });

			} else {

				console.log("User Id not found.");
			}

	    }

	    $scope.getSelectedStates = function (countryId) {
	    	
	    	var Obj = {"countryId":countryId};

			$http.post('api/country/getStates',Obj).success(function(response) {

				if(response.STATUS == 1)
				{
					$scope.states = response.DATA;
				}
				else
				{
					$scope.states = "";
				}

			}).error(function(response) {

		    	$scope.states = "";
		    });
		};


		$scope.getSelectedCities = function (stateId) {
	    
	    	var Obj = {"stateId":stateId};

			$http.post('api/country/getCities',Obj).success(function(response) {

				if(response.STATUS == 1)
				{
					$scope.cities = response.DATA;
				}
				else
				{
					$scope.cities = "";
				}

			}).error(function(response) {

		    	$scope.cities = "";
		    });
		};

	    $scope.userDetailsFreelancer = function (user) {
	    	
	    	var userObj = $scope.user;

			$http.post('api/freelancer/updateFreelancer',userObj).success(function(response) {

				if(response.STATUS == 1)
				{				
					$location.path('/freelancer/profile/dashboard');
					Flash.create('success', response.MESSAGE);
				}
				else
				{
					$location.path('/freelancer/profile/update');
					Flash.create('danger', response.MESSAGE);
				}

			}).error(function(response) {
				
				Flash.create('danger', response.MESSAGE);
		    	$location.path('/freelancer/profile/update');
		    });
	    };

	    $scope.getAllSkills = function (stateId) {
	    
	    	var Obj = {};

			$http.post('api/user/getAllSkills',Obj).success(function(response) {

				if(response.STATUS == 1)
				{
					$scope.skills = response.DATA;
				}
				else
				{
					$scope.skills = "";
				}

			}).error(function(response) {

		    	$scope.skills = "";
		    });
		};

		$scope.getQualifications = function (stateId) {
	    
	    	var Obj = {};

			$http.post('api/user/getQualifications',Obj).success(function(response) {

				if(response.STATUS == 1)
				{
					$scope.qualifications = response.DATA;
				}
				else
				{
					$scope.qualifications = ""
				}

			}).error(function(response) {

		    	$scope.qualifications ="";
		    });
		};

		$scope.updateCompany = function (user) {
	    	
	    	var companyObj ={

	    		user_id 					: user.user_id,
	    		company_no_of_employer 		: user.company_no_of_employer,
	    		company_type 				: user.company_type,
	    		vat_number 	 				: user.vat_number,
	    		commercial_register_number  : user.commercial_register_number,
	    		company_address 			: user.company_address,
	    	}   	

			$http.post('api/freelancer/updateCompany',companyObj).success(function(response) {

				if(response.STATUS == 1)
				{				
					$location.path('/freelancer/profile/dashboard');
					Flash.create('success', response.MESSAGE);
				}
				else
				{
					$location.path('/freelancer/profile/companyDetails');
					Flash.create('danger', response.MESSAGE);
				}

			}).error(function(response) {
				
				Flash.create('danger', response.MESSAGE);
		    	$location.path('/freelancer/profile/companyDetails');
		    });
	    };

	    $scope.changePasssword = function (user) {

	    	if(user.user_id && user.user_id != null && user.user_id != 'undefiend')
	    	{
		    	var passwordObj ={

		    		user_id 			: user.user_id,
		    		old_password 		: user.old_password,
		    		password 			: user.password,
		    		confirm_password 	: user.confirm_password,
		    		
		    	} 

				$http.post('api/freelancer/changePasssword',passwordObj).success(function(response) {

					if(response.STATUS == 1)
					{				
						$location.path('/freelancer/profile/dashboard');
						Flash.create('success', response.MESSAGE);
					}
					else
					{
						$location.path('/freelancer/profile/changePassword');
						Flash.create('danger', response.MESSAGE);
					}

				}).error(function(response) {
					
					Flash.create('danger', response.MESSAGE);
			    	$location.path('/freelancer/profile/changePassword');
			    });
			}
			else
			{
				$location.path('/login');
			}
	    };
	    
	    /* 
	    ** Freelancer Dashboard Management
	    **
	    */


	    $scope.getDashboardData = function () {
	    	$scope.getAllProjects();
	    	$scope.getAllCategories();
	    	$scope.getAllSkills();
	    	$scope.getAllLocations();	    	
	    }

	    $scope.getAllProjects = function () {
	    	
	    	var projectObj = {};

    		$http.post('api/freelancer/getAllProjects',projectObj).success(function(response) {

				if(response.STATUS == 1)
				{
					$scope.projectListData = angular.fromJson(response.DATA);
				}			

			}).error(function(response) {
				
				
		    });			
	    }

	    $scope.getAllCategories = function () { 
	    	var Obj = {}; 
			$http.post('api/user/getAllCategories',Obj).success(function(response) { 
				if(response.STATUS == 1)
				{
					$scope.categories = response.DATA;
				}
				else
				{
					$scope.categories = "";
				}
			}).error(function(response) { 
		    	$scope.categories = "";
		    });
		};

		$scope.getAllSkills = function () { 
	    	var Obj = {}; 
			$http.post('api/user/getAllSkills',Obj).success(function(response) { 
				if(response.STATUS == 1)
				{
					$scope.skills = response.DATA;
				}
				else
				{
					$scope.skills = "";
				} 
			}).error(function(response) { 
		    	$scope.skills = "";
		    });
		};

		$scope.getAllLocations = function () { 
	    	
	    	var Obj = {};

	    	if(allLocations == null && allLocations =="")
	    	{
				$http.post('api/user/getAllLocations',Obj).success(function(response) { 
					if(response.STATUS == 1)
					{					
						$localStorage.set('allLocations',response.DATA);
					}
					else
					{
						$localStorage.put('allLocations',"");
					} 
				}).error(function(response) {

			    	$localStorage.put('allLocations',"");
			    });
			}
		};



		/* 
	    ** Freelancer Project View Management
	    **
	    */

	     $scope.getProjectDetailsById = function () {

	    	var projectId = $routeParams.id;

	    	if(projectId && projectId != '' && projectId != 'undefiend')
	    	{
		    	var projDetailObj = {'pid':projectId};

	    		$http.post('api/freelancer/projectDetailsById',projDetailObj).success(function(response) {

					if(response.STATUS == 1)
					{
						$scope.projectData  = response.DATA[0];
						$scope.skills 		= response.DATA.skills;
						$scope.categories   = response.DATA.categories;
						$scope.jobImages    = response.DATA.job_images;
						$scope.jobDocuments = response.DATA.job_documents;
					}
					$scope.loading  = false;		

				}).error(function(response) {
					
					$location.path('/freelancer/profile/dashboard');
			    });	
		    }
		    else
		    {
		    	$location.path('/freelancer/profile/dashboard');
		    }		
	    }

	    $scope.getSkillsByArray = function (skillsArray) {
	    	
	    	var Obj = {'skillsArray':skillsArray};
	    	

			$http.post('api/freelancer/getSkillsByArray',Obj).success(function(response) { 
				if(response.STATUS == 1)
				{
					$scope.jobSkills = response.DATA;
					
				}
				else
				{
					$scope.jobSkills = "";
				}

				$scope.loading  = false;
			}).error(function(response) { 
		    	$scope.jobSkills = "";
		    });	    
	    }

	    $scope.jobApply = function (projectId) {
	    	var currentUserId = $scope.user.userId;
	    	var projectId = projectId;
	    	alert(projectId);
	    }

	    $scope.jobApplyFrom = function (value) {
	    	
	    	if(value == true)
	    	{
	    		$scope.ApplyFormToggle  = true;
	    	}

	    	if(value == false)
	    	{
	    		$scope.ApplyFormToggle  = false;
	    	}
	    		    }

	   

	   
	    
	}
]);

freelancerApp.directive('onErrorSrc', function() {
    return {
        link: function(scope, element, attrs) {
          element.bind('error', function() {
            if (attrs.src != attrs.onErrorSrc) {
              attrs.$set('src', attrs.onErrorSrc);
            }
          });
        }
    }
});
