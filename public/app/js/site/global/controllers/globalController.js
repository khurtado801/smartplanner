var globalApp = angular.module('globalModule');
globalApp.controller('globalController', ['$scope', '$location', '$http', '$routeParams','$localStorage', 
	function($scope)
	{
		$scope.$defaultlanguage = true;	
		$scope.changeLanguage = function (langKey) {				
			$translate.use(langKey);
	    };
	}
]);
