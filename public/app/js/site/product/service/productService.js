angular.module('productService', []).factory('home', ['$resource',
  function ($resource) {
    return $resource('/api/user/:userId', {
      userId: '@id'
    }, {
      /*searchload: {
        method: 'POST',
        url: '/home/homepagedata',
      }*/
    });
  }
]);
