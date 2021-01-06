$(document).ready(function() {
        $.validator.addMethod("nospace", function(value, element) {
                return value.indexOf(" ") < 0 && value != "";
        }, "Space is not allowed");
        $.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
        }, "only alpha numeric allowed"); 

    function ajaxLoader (el, options) {
		// Becomes this.options
		var defaults = {
			bgColor 		: '#fff',
			duration		: 100,
			opacity			: 0.7,
			classOveride 	: true
		}
		this.options 	= jQuery.extend(defaults, options);
		this.container 	= $(el);
		
		this.init = function() {
			var container = this.container;
			pluswidth = window.pluswidth || 0;
			var width_con = container.width() + pluswidth;
			var plusheight = window.plusheight || 0;
			var height_con = container.prop('scrollHeight') + plusheight;
			// Delete any other loaders
			this.remove(); 
			var position = $(el).position();
			minus = window.minusheight || 0;
			// Create the overlay 
			var overlay = $('<div></div>').css({
					'background-color': this.options.bgColor,
					'opacity':this.options.opacity,
					'width':width_con,
					'height':height_con,
					'position':'absolute',
					//'top':position.top-minus ,
					'top':position.top-minus ,
					'left':position.left,
					'z-index':99999
			}).addClass('ajax_overlay');
			// add an overiding class name to set new loader style 
			if (this.options.classOveride) {
				overlay.addClass(this.options.classOveride);
			}
			// insert overlay and loader into DOM 
			container.append(
				overlay.append(
					$('<div></div>').addClass('ajax_loader')
				).fadeIn(this.options.duration)
			);
	    };
		
		this.remove = function(){
			var overlay = this.container.children(".ajax_overlay");
			if (overlay.length) {
				overlay.fadeOut(this.options.classOveride, function() {
					overlay.remove();
				});
			}	
		}

	    this.init();
	}
});

jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

function getFormattedDate(date) {
  var year = date.getFullYear();
  var month = (1 + date.getMonth()).toString();
  month = month.length > 1 ? month : '0' + month;
  var day = date.getDate().toString();
  day = day.length > 1 ? day : '0' + day;
  return month + '-' + day + '-' + year;
} 

/*
$(document).ready(function() { 
    function ajaxLoader (el, options) {
		// Becomes this.options
		var defaults = {
			bgColor 		: '#fff',
			duration		: 100,
			opacity			: 0.7,
			classOveride 	: true
		}
		this.options 	= jQuery.extend(defaults, options);
		this.container 	= $(el);
		
		this.init = function() {
			var container = this.container;
			pluswidth = window.pluswidth || 0;
			var width_con = container.width() + pluswidth;
			var plusheight = window.plusheight || 0;
			var height_con = container.prop('scrollHeight') + plusheight; 
			this.remove(); 
			var position = $(el).position();
			minus = window.minusheight || 0;
			// Create the overlay 
			var overlay = $('<div></div>').css({
					'background-color': this.options.bgColor,
					'opacity':this.options.opacity,
					'width':width_con,
					'height':height_con,
					'position':'absolute', 
					'top':position.top-minus ,
					'left':position.left,
					'z-index':99999
			}).addClass('ajax_overlay');
			// add an overiding class name to set new loader style 
			if (this.options.classOveride) {
				overlay.addClass(this.options.classOveride);
			}
			// insert overlay and loader into DOM 
			container.append(
				overlay.append(
					$('<div></div>').addClass('ajax_loader')
				).fadeIn(this.options.duration)
			);
	    };
		
		this.remove = function(){
			var overlay = this.container.children(".ajax_overlay");
			if (overlay.length) {
				overlay.fadeOut(this.options.classOveride, function() {
					overlay.remove();
				});
			}	
		}

	    this.init();
	}
});*/

