var productModule = angular.module('productModule', ['ngRoute']);
productModule.config(function($routeProvider) {
	$routeProvider
		/*.when('/list', {
			templateUrl : 'templates/site/product/listpage.html',
			controller  : 'productController'
		})*/
		.when('/productdetail', {
			templateUrl : 'public/app/templates/site/product/productdetail.html',
			controller  : 'productController'
		})
		.when('/productgrid', {
			templateUrl : 'public/app/templates/site/product/gridpage.html',
			controller  : 'productController'
		})
		.when('/productlist', {
			templateUrl : 'public/app/templates/site/product/listpage.html',
			controller  : 'productController'
		})
		.when('/cartpage', {
			templateUrl : 'public/app/templates/site/product/cartpage.html',
			controller  : 'productController'
		}) 
		.otherwise({
            redirectTo: '/public/app/templates/site/home/home.html'
        })
		/*.when('/paymentpage', {
			templateUrl : 'templates/site/product/cartpage.html',
			controller  : 'productController'
		})	*/	
});

