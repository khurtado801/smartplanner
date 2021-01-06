angular.module('registerService', []).factory('register', ['$resource',
  function ($resource) {
    return $resource('/api/user/:userId', {
      userId: '@id'
    }, {
      searchload: {
        method: 'POST',
        url: '/register',
      }
    });
  }
]);
