var editorApp = angular.module('ModificationPageModule');

editorApp.controller('editorController', function($scope) {
  $scope.summary = 'Initial content';

  $scope.getContent = function() {
    console.log('Editor content:', $scope.summary);
  };

  $scope.setContent = function() {
    $scope.summary = 'Time: ' + (new Date());
  };

  /*$scope.tinymceOptions = {
    plugins: 'link image code',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code',
    resize: false,
    width: 700,  // I *think* its a number and not '400' string
    height: 200,    
  };*/

  $scope.tinymceOptions ={      
      menubar : false,
      statusbar : false,
      toolbar: "dummyimg | bold italic underline | formatselect | fontsizeselect | bullist numlist | outdent indent | link jbimages | code",
      plugins : 'advlist autolink link lists charmap code jbimages',      
  };
  
});
