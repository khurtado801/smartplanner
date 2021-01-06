'use strict';

angular.module('truckForLoad')
  .factory('User', function ($resource) {
    return $resource('/api/user/:id/:controller', {
      id: '@_id'
    },
    {
      changePassword: {
        method: 'PUT',
        params: {
          controller:'password'
        }
      },
      get: {
        method: 'GET',
        params: {
          id:'me'
        }
      },
      getByToken: {
        method: 'GET',
        url: '/api/user/getByToken'
      },
      getFormToken: {
        method: 'GET',
        url: '/api/user/getFormToken'
      },
	  });
  });
