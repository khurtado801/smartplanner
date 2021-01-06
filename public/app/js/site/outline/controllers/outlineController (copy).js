//var outlineModule = angular.module('myApps',['ui.tinymce']);
var outlineModule = angular.module('outlinePageModule');
outlineModule.controller('TinyMceController', ['$scope', function ($scope){   
	$scope.tinymceModel = 'Initial content';

	$scope.getContent = function() {
		console.log('Editor content:', $scope.tinymceModel);
	};

	$scope.setContent = function() {
		$scope.tinymceModel = 'Time: ' + (new Date());
	};

	$scope.tinymceOptions = {
		plugins: 'link image code',
		toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code'
	};
  $scope.scrollbarConfig = {
    theme: 'dark',
    scrollInertia: 500
  };

}]);

//***********************************************
// Tab - Menu
//***********************************************

angular.module('ui.bootstrap.demo', ['ngAnimate', 'ngSanitize', 'ui.bootstrap']);
angular.module('ui.bootstrap.demo').controller('TabsDemoCtrl', function ($scope, $window) {
  $scope.tabs = [
    { title:'Dynamic Title 1', content:'Dynamic content 1' },
    { title:'Dynamic Title 2', content:'Dynamic content 2', disabled: true }
  ];

  $scope.alertMe = function() {
    setTimeout(function() {
      $window.alert('You\'ve selected the alert tab!');
    });
  };

  $scope.model = {
    name: 'Tabs'
  };
});