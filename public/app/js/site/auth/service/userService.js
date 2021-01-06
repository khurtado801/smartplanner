'use strict';

angular.module('truckForLoad')
  .factory('User', function ($resource,config) {
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
        url: config.url+'user/getByToken'
      },
      getFormToken: {
        method: 'GET',
        url: config.url+'user/getFormToken'
      },
	  });
  });
