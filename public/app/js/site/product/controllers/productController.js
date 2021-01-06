 
var productModule = angular.module('productModule');

productModule.controller('productController', ['$window', '$timeout', '$interval', '$scope', '$location', '$http', '$routeParams', '$localStorage', '$sessionStorage',
	
	function( $window, $timeout, $interval, $scope, $location, $http, $routeParams, $localStorage, $sessionStorage) {
		
		$scope.loading  = false;
		$scope.prodObj  = [];
		
		if($localStorage.cartRecListData && $localStorage.cartRecListData.length != null && $localStorage.cartRecListData.length > 0)
		{
			$scope.cartRecListData = $localStorage.cartRecListData;

		} else {

			$scope.cartRecListData = [];
		}

		if($localStorage.cartListData && $localStorage.cartListData.length != null && $localStorage.cartListData.length > 0)
		{
			$scope.cartListData = $localStorage.cartListData;

		} else {
			
			$scope.cartListData = [];
		}

		/* 
		* Home Page New Arrivals Listing
		* Home Page Middle section
		*/

		$scope.getNewArrivalsProducts = function () {
			var url = config.baseurl;
			var upcCode = {};
			var promise = $http.get(url+'newarrivals');
			
			promise.then(
				function(response) {

					if(response.data.length > 0)
					{				  	
						$scope.newArrivalsProductlist  = response.data;
						
					} else {

						$scope.newArrivalsProductlist  = "notFound";
					}
					$scope.loading        		   = false; 
				},
				function(error) {

					$scope.newArrivalsProductlist  	   = "notFound";
					$scope.loading        			   = false;					
				}	  			
				);
		};

		/* 
		* List / Grid Page Listing
		*
		*/

		$scope.getListpageProducts = function () {
			
			var category 			= {dept:$routeParams.dept};	
			var promise 			= $http.post('http://115.115.91.46:5000/items', category);
			$scope.productCategory  = category.dept;

			promise.then(				
				function(response) {

					if(response.data.length > 0)
					{     
						$scope.productlist     = response.data;		        	
						$scope.loading         = false;	        		
					} 
					else
					{
						$scope.loading          = false;
						$scope.productlist  	= "notFound";

					}
				},
				function(error) {

					$scope.loading          = false;
					$scope.productlist  	= "notFound";		    	
				}
				);

		};

		/* 
		* Product Detail
		* Product Detail Page View Product
		*/

		$scope.getProductDetails = function () {	    	

			var upcCode  			 = {upc:$routeParams.upc};
			var category 			 = {dept:$routeParams.dept};					     

			$scope.allProducts  	 = $localStorage.prodData;
			$scope.productCategory   = category.dept;

			if($localStorage.prodData.length > 0){

				$scope.productDetails   = $scope.allProducts.filter(function (product){
					return (product.upc == upcCode.upc);
				});			
				
				$scope.loading           = false;

			} else {
				
				$scope.productDetails  	 = "notFound";
				$scope.loading           = false;
			}
		};

		/*
		* Recommendad Products
		* Product Detail Page bottom section
		*/


		$scope.getRecommendadProducts = function () {
			/*var obj = {"upcs": ["305213091006"]};*/			
			var upcCode     = {"upcs": $routeParams.upc};
			var category    = {dept:$routeParams.dept};
			var promise 	= $http.post('http://115.115.91.46:5000/predict', upcCode)	    		     

			promise.then(
				function(response) {
					if(response.data.length > 0)
					{     
						$scope.loading        	 = false;			    	
						$scope.recProductlist    = response.data;			        	    	
					}
					else
					{
						$scope.loading        	 = false;
						$scope.recProductlist  	 = "notFound";
					}  	        	

				},
				function(response) {

					$scope.loading         = false;		       
					$scope.recProductlist  = "notFound";
					console.log("Some Problem in recommendad product listing");

				}
				);
		};

	    /*
		* Add to Cart Page
		* listing of add to cart product listing.
		*/

		$scope.getCartDetails = function () { 

			if($scope.cartListData && $scope.cartListData.length > 0 && $scope.cartListData != null )
			{
				var productPrice = 0;
				for(var i = 0; i < $scope.cartListData.length; i++){

					if($scope.cartListData[i].salePrice != null)
					{
						productPrice += $scope.cartListData[i].salePrice;
					}
				}

				$scope.cartTotal	= productPrice;
				$scope.loading      = false;			    	
				$scope.cartData     = $scope.cartListData;								  		        	
			}
			else
			{
				$scope.loading      = false;
				$scope.cartData  	= "notFound";
				$scope.cartTotal  	= "0.00";
			}      	

		};

		/*
		* Cart Based Recommendad Products
		* Cart Page bottom section
		*/

		$scope.getRelatedProducts = function () {

			$scope.allCartData    = $scope.cartRecListData;
			var upcCode 		  = {"upcs": $scope.allCartData};
			var promise = $http.post('http://115.115.91.46:5000/cart', upcCode);
			
			promise.then(
				function(response) {

					if(response.data.length > 0)
					{     
						$scope.loading        	 = false;			    	
						$scope.relProductlist    = response.data;		        	
					}
					else
					{
						$scope.loading        	 = false;
						$scope.relProductlist  	 = "notFound";
					}
				}, 
				function(response) {

					$scope.relProductlist  = "notFound";
					$scope.loading         = false;	
				}
				);
		};

		/*
		* Add to Cart
		* Add to Cart For all button click on add to cart.
		*/		

		$scope.addToCart = function (upc, price, cartDataObj, cartpage) {
			
			if($scope.cartListData && $scope.cartListData.length < 10 )
			{
				if(price != null && price != 'undefiend' )
				{				
					if(upc)
					{
						if(confirm('Intrested for Add to cart'))
						{
							$scope.cartRecListData.push(upc);			
							$localStorage.cartRecListData  = $scope.cartRecListData;

							$scope.cartListData.push(cartDataObj);			
							$localStorage.cartListData  = $scope.cartListData;

							/*var cartUrl = 'cartpage';				
							$location.url(cartUrl);*/

							if(cartpage == 2 )
							{
								$window.location.reload();
							}							
						}
						else {
							
							return false;
						}

					} else {
						
						return false;
					}

				} else {

					alert('!! Price not available cannot add to cart. !!');
					return false;
				}

			} else {

				alert('!! Cannot More than 10 Product add to cart once. !!');
				return false;
			}
		};


		/*
		* Add to Cart Page
		* Clear shopping cart clear all teh cart related data from local storage
		*/	    

		$scope.clearShoppingCart = function () { 

			if(confirm('Are you sure want to empty your cart.'))
			{
				$localStorage.cartListData    = [];
				$localStorage.cartRecListData = [];
				$location.path('/');

				return true;
			}
			else
			{
				return false;
			}
		};

		/*
		* Add to Cart Page
		* delete one buy one selected product form shopping cartpage.
		*/

		$scope.deleteProduct = function (upc, cartDataObj) {

			if(upc)
			{				
				if(confirm('Sure to remove this product from cart.'))
				{	
					/*
					* Add to Cart Page
					* Delete cart based recomanded upc form local storage
					*/

					if($scope.cartRecListData && $scope.cartRecListData.length != "" && $scope.cartRecListData.length > 0)
					{
						for(var j = 0; j < $scope.cartRecListData.length; j++){

							if($scope.cartRecListData[j] && $scope.cartRecListData[j] != null)
							{								
								if($scope.cartRecListData[j] == upc)
								{	
									$scope.cartRecListData.splice(j, 1);	
									$localStorage.cartRecListData  = $scope.cartRecListData;									

								} 

							} else {

								console.log('Problem to recomanded product id delete form local storage');
								return false;
							}
						}						

					} else {

						console.log('Problem to recomanded product id delete form local storage');
						return false;
					}

					/*
					* Add to Cart Page 
					* Delete obj From Cart and also deleted object form local storage
					*/

					if($scope.cartListData && $scope.cartListData.length != "" && $scope.cartListData.length > 0)
					{	
						for(var i = 0; i < $scope.cartListData.length; i++){

							if($scope.cartListData[i] && $scope.cartListData[i] != null)
							{
								if($scope.cartListData[i].upc == upc)
								{
									$scope.cartListData.splice(i, 1);	
									$localStorage.cartListData  = $scope.cartListData;

									break;
								} 

							} else {

								alert('Problem to remove this product form cart');
								return false;
							}				

						}						

					} else {

						alert('Problem to remove this product form cart');
						return false;
					}
					$scope.loading         = true;	
					$window.location.reload();

				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}		

		/*
		* All Sections for product detail page
		* Storage of product object for view page in local storage
		*/		

		$scope.storeDataIntoStorage = function (data, upc) {		
			
			if(data && upc && data != null && upc != null && data != 'undefiend' && upc != 'undefiend')
			{				
				$scope.prodObj.push(data);			
				$localStorage.prodData    = $scope.prodObj;
				var path = 'productdetail?upc='+upc;				
				$location.url(path);

			} else {

				console.log('Problem to saving data into local stoarage.');
				return false;
			}			
		};

		/*
		* Cart page
		* Show Pop up of functionaly not developed section.
		*/

		$scope.notAllowed = function () {

			var notAllowedMsg = '!! Sorry, Functionality not developed !!';
			alert(notAllowedMsg);
		}
	}
]);

productModule.filter('ifEmpty', function() {

	return function(input, defaultValue) {
		if (angular.isUndefined(input) || input === null || input === '') {
			return defaultValue;
		}

		return input;
	}
});

productModule.directive('fallbackSrc', function () {

	var fallbackSrc = {
		link: function postLink(scope, iElement, iAttrs) {
			iElement.bind('error', function() {
				angular.element(this).attr("src", iAttrs.fallbackSrc);
			});
		}
	}
	return fallbackSrc;
});

productModule.directive('ngLightbox', ['$compile', function($compile) {

	return function(scope, element, attr) {
		var lightbox, options, overlay;

		var defaults = {
			'class_name': false,
			'trigger': 'manual',
			'element': element[0],
			'kind': 'normal'
		}

		var options = angular.extend(defaults, angular.fromJson(attr.ngLightbox));

		// check if element is passed by the user
		options.element = typeof options.element === 'string' ? document.getElementById(options.element) : options.element;

		var add_overlay = function(){
			if(document.getElementById('overlay')) return;
			// compiling when we add it to have the close directive kick in
			overlay = $compile('<div id="overlay" ng-lightbox-close/>')(scope);
			
			// add a custom class if specified
			options.class_name && overlay.addClass(options.class_name);

			// append to dom
			angular.element(document.body).append(overlay);

			// load iframe options if defined
			options.kind === 'iframe' && load_iframe();

			// we need to flush the styles before applying a class for animations
			window.getComputedStyle(overlay[0]).opacity;
			overlay.addClass('overlay-active');
			angular.element(options.element).addClass('lightbox-active');
		}

		var load_iframe = function(){
			options.element = options.element || 'lightbox-iframe';
			var iframe = "<div id='" + options.element + "' class='lightbox'><iframe frameBorder=0 width='100%' height='100%' src='" + attr.href + "'></iframe></div>";
			angular.element(document.body).append(iframe)
		}

		if(options.trigger === 'auto'){
			add_overlay();
		}else{
			element.bind('click', function(event) {
				add_overlay();
				event.preventDefault();
				return false;
			});
		}
	}
}]);

productModule.directive('ngLightboxClose', function() {
	return function(scope, element, attr) {
		var transition_events = ['webkitTransitionEnd', 'mozTransitionEnd', 'msTransitionEnd', 'oTransitionEnd', 'transitionend'];

		angular.forEach(transition_events, function(ev){
			element.bind(ev, function(){
				// on transitions, when the overlay doesnt have a class active, remove it
				!angular.element(document.getElementById('overlay')).hasClass('overlay-active') && angular.element(document.getElementById('overlay')).remove();
			});
		});

		// binding esc key to close
		angular.element(document.body).bind('keydown', function(event){
			event.keyCode === 27 && remove_overlay();
		});

		// binding click on overlay to close
		element.bind('click', function(event) {
			remove_overlay();
		});

		var remove_overlay = function(){
			var overlay = document.getElementById('overlay');
			angular.element(document.getElementsByClassName('lightbox-active')[0]).removeClass('lightbox-active');

			// fallback for ie8 and lower to handle the overlay close without animations
			if(angular.element(document.documentElement).hasClass('lt-ie9')){
				angular.element(overlay).remove();
			}else{
				angular.element(overlay).removeClass('overlay-active');
			}
		}
	}
});


//var productModule = angular.module('productModule');
productModule.directive('wrapOwlcarousel', function () {

	var link = function (scope, element, attr) {    	
		/*	alert(attr.items);*/
		// Loads owl carousel with default settings, unless otherwise requested in parameters
		var carousel = function () {
			element.owlCarousel({
				// Most important owl features
				items: attr.items ? attr.items : 5,
				itemsCustom: attr.owlItemscustom ? (attr.owlItemscustom.toLowerCase() == 'true') : false,
				itemsDesktop: [1199, attr.owlItemsdesktop ? attr.owlItemsdesktop : 4],
				itemsDesktopSmall: [980, attr.owlItemsdesktopsmall ? attr.owlItemsdesktopsmall : 3],
				itemsTablet: [768, attr.owlItemstablet ? attr.owlItemstablet : 2],
				itemsTabletSmall: attr.owlItemstabletsmall ? (attr.owlItemstabletsmall.toLowerCase() == 'true') : false,
				itemsMobile: [479, attr.owlItemsmobile ? attr.owlItemsmobile : 1],
				singleItem: attr.owlSingleitem ? (attr.owlSingleitem.toLowerCase() == 'true') : false,
				itemsScaleUp: attr.owlItemsscaleup ? (attr.owlItemsscaleup.toLowerCase() == 'true') : false,

				//Basic Speeds
				slideSpeed: attr.owlSlidespeed ? attr.owlSlidespeed : 200,
				paginationSpeed: attr.owlPaginationspeed ? attr.owlPaginationspeed : 800,
				rewindSpeed: attr.owlRewindspeed ? attr.owlRewindspeed : 1000,

				//Autoplay
				autoPlay: attr.owlAutoplay ? (attr.owlAutoplay.toLowerCase() == 'true') : false,
				stopOnHover: attr.owlStoponhover ? (attr.owlStoponhover.toLowerCase() == 'true') : false,

				// Navigation
				navigation: attr.owlNavigation ? (attr.owlNavigation.toLowerCase() == 'true') : false,
				navigationText: [attr.owlNavigationtextprev ? attr.owlNavigationtextprev : "prev",
				attr.owlNavigationtextnext ? attr.owlNavigationtextnext : "next"],
				rewindNav: attr.owlRewindnav ? (attr.owlRewindnav.toLowerCase() == 'true') : true,
				scrollPerPage: attr.owlScrollperpage ? (attr.owlScrollperpage.toLowerCase() == 'true') : false,

				//Pagination
				pagination: attr.owlPagination ? (attr.owlPagination.toLowerCase() == 'true') : true,
				paginationNumbers: attr.owlPaginationnumbers ? (attr.owlPaginationnumbers.toLowerCase() == 'true') : false,

				// Responsive
				responsive: attr.owlResponsive ? (attr.owlResponsive.toLowerCase() == 'true') : true,
				responsiverefreshrate: attr.owlResponsiverefreshrate ? attr.owlResponsiverefreshrate : 200,
				responsivebasewidth: attr.owlResponsivebasewidth ? attr.owlResponsivebasewidth : window,

				// CSS Styles
				baseClass: attr.owlBaseclass ? attr.owlBaseclass : "owl-carousel",
				theme: attr.owlTheme ? attr.owlTheme : "owl-theme",

				//Lazy load
				lazyLoad: attr.owlLazyload ? (attr.owlLazyload.toLowerCase() == 'true') : false,
				lazyFollow: attr.owlLazyfollow ? (attr.owlLazyfollow.toLowerCase() == 'true') : true,
				lazyEffect: attr.owlLazyeffect ? attr.owlLazyeffect : "fade",

				//Auto height
				autoHeight: attr.owlAutoheight ? (attr.owlAutoheight.toLowerCase() == 'true') : false,

				//JSON
				jsonPath: attr.owlJsonpath ? (attr.owlJsonpath.toLowerCase() == 'true') : false,
				jsonSuccess: attr.owlJsonsuccess ? (attr.owlJsonsuccess.toLowerCase() == 'true') : false,

				//Mouse Events
				dragBeforeAnimFinish: attr.owlDragbeforeanimfinish ? (attr.owlDragbeforeanimfinish.toLowerCase() == 'true') : true,
				mouseDrag: attr.owlMousedrag ? (attr.owlMousedrag.toLowerCase() == 'true') : true,
				touchDrag: attr.owlTouchdrag ? (attr.owlTouchdrag.toLowerCase() == 'true') : true,

				//Transitions
				transitionStyle: attr.owlTransitionstyle ? (attr.owlTransitionstyle.toLowerCase() == 'true') : false,

				// Other
				addClassActive: attr.owlAddclassactive ? (attr.owlAddclassactive.toLowerCase() == 'true') : false,

				//Callbacks
				beforeUpdate: attr.owlBeforeupdate ? (attr.owlBeforeupdate.toLowerCase() == 'true') : false,
				afterUpdate: attr.owlAfterupdate ? (attr.owlAfterupdate.toLowerCase() == 'true') : false,
				beforeInit: attr.owlBeforeinit ? (attr.owlBeforeinit.toLowerCase() == 'true') : false,
				afterInit: attr.owlAfterinit ? (attr.owlAfterinit.toLowerCase() == 'true') : false,
				beforeMove: attr.owlBeforemove ? (attr.owlBeforemove.toLowerCase() == 'true') : false,
				afterMove: attr.owlAftermove ? (attr.owlAftermove.toLowerCase() == 'true') : false,
				afterAction: attr.owlAfteraction ? (attr.owlAfteraction.toLowerCase() == 'true') : false,
				startDragging: attr.owlStartdragging ? (attr.owlStartdragging.toLowerCase() == 'true') : false,
				afterLazyLoad: attr.owlAfterlazyload ? (attr.owlAfterlazyload.toLowerCase() == 'true') : false
			});
}

		// Use carousel's id to bind control buttons to specific carousel (Multiple carousel support)
		// Otherwise, use owl-carousel as default.
		// Any element with given class will trigger control on click.
		//  '.owlcarousel-next' - Scrolls left
		//  '.owlcarousel-prev' - Scrolls right
		//  '.owlcarousel-play' - Starts autoplay
		//  '.owlcarousel-stop' = Stops autoplay
		var uniqueId = attr.id ? attr.id : 'owl-carousel';
		var actions = function () {
			angular.element("#nextItem").click(function () {
				element.trigger('owl.next');
			})
			angular.element("#prevItem").click(function () {
				element.trigger('owl.prev');
			})

		}

		// Watch items in carousel to reload when items are added/removed.
		scope.$watch(uniqueId + "-items", function (value) {
			carousel(element);
		})

		// Load the triggers for carousel controls.
		actions();
	}

	return {
		restrict: 'E',
		link: link
	}

});
	
