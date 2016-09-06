(function ($) {
	"use strict";

    // Checks for ie
    if ( !!navigator.userAgent.match(/MSIE/i) || !!navigator.userAgent.match(/Trident.*rv:11\./) ){
    	$('body').addClass('ie');
    }

    // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
    var ua = window['navigator']['userAgent'] || window['navigator']['vendor'] || window['opera'];
    if( (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua) ){
    	$('body').addClass('smart');
	} 

})(jQuery);

(function ($) {
	"use strict";

	$('input, textarea').each(function(){
		$(this).val() ? $(this).addClass('has-value') : $(this).removeClass('has-value');
	});
	$(document).on('blur', 'input, textarea', function(e){
		$(this).val() ? $(this).addClass('has-value') : $(this).removeClass('has-value');
	});

})(jQuery);

(function ($) {
  "use strict";
  
  $(document).on('click', '[ui-nav] a', function (e) {
    var $this = $(e.target), $active, $li;
    $this.is('a') || ($this = $this.closest('a'));
    
    $li = $this.parent();
    $active = $li.siblings( ".active" );
    $li.toggleClass('active');
    $active.removeClass('active');
  });
})(jQuery);


/**
 * 0.1.0
 * Deferred load js/css file, used for ui-jq.js and Lazy Loading.
 * 
 * @ flatfull.com All Rights Reserved.
 * Author url: http://themeforest.net/user/flatfull
 */
var uiLoad = uiLoad || {};

(function($, $document, uiLoad) {
	"use strict";

	var loaded = [],
	promise = false,
	deferred = $.Deferred();

	/**
	 * Chain loads the given sources
	 * @param srcs array, script or css
	 * @returns {*} Promise that will be resolved once the sources has been loaded.
	 */
	uiLoad.load = function (srcs) {
		srcs = $.isArray(srcs) ? srcs : srcs.split(/\s+/);
		if(!promise){
			promise = deferred.promise();
		}

		$.each(srcs, function(index, src) {
			promise = promise.then( function(){
				return src.indexOf('.css') >=0 ? loadCSS(src) : loadScript(src);
			} );
		});
		deferred.resolve();
		return promise;
	};

	/**
	 * Dynamically loads the given script
	 * @param src The url of the script to load dynamically
	 * @returns {*} Promise that will be resolved once the script has been loaded.
	 */
	var loadScript = function (src) {
		if(loaded[src]) return loaded[src].promise();

		var deferred = $.Deferred();
		var script = $document.createElement('script');
		script.src = src;
		script.onload = function (e) {
			deferred.resolve(e);
		};
		script.onerror = function (e) {
			deferred.reject(e);
		};
		$document.body.appendChild(script);
		loaded[src] = deferred;

		return deferred.promise();
	};

	/**
	 * Dynamically loads the given CSS file
	 * @param href The url of the CSS to load dynamically
	 * @returns {*} Promise that will be resolved once the CSS file has been loaded.
	 */
	var loadCSS = function (href) {
		if(loaded[href]) return loaded[href].promise();

		var deferred = $.Deferred();
		var style = $document.createElement('link');
		style.rel = 'stylesheet';
		style.type = 'text/css';
		style.href = href;
		style.onload = function (e) {
			deferred.resolve(e);
		};
		style.onerror = function (e) {
			deferred.reject(e);
		};
		$document.head.appendChild(style);
		loaded[href] = deferred;

		return deferred.promise();
	}

})(jQuery, document, uiLoad);

(function ($) {
	"use strict";

	$(document).on('click', '[ui-fullscreen]', function (e) {
		e.preventDefault();
		if (screenfull.enabled) {
		  screenfull.toggle();
		}
	});
})(jQuery);

(function ($) {
	"use strict";
  	$.extend( jQuery.easing,{
	    def: 'easeOutQuad',
	    easeInOutExpo: function (x, t, b, c, d) {
	        if (t==0) return b;
	        if (t==d) return b+c;
	        if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
	        return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	    }
	});

	$(document).on('click', '[ui-scroll-to]', function (e) {
		e.preventDefault();
		var target = $('#'+$(this).attr('ui-scroll-to'));
		$('html,body').animate({
          scrollTop: target.offset().top
        }, 600, 'easeInOutExpo');
	});
})(jQuery);

(function ($) {
  	"use strict";
  
	$.fn.uiJp = function(){

		var lists  = this;

        lists.each(function()
        {
        	var self = $(this);
			var options = eval('[' + self.attr('ui-options') + ']');
			if ($.isPlainObject(options[0])) {
				options[0] = $.extend({}, options[0]);
			}

			// uiLoad.load(MODULE_CONFIG[self.attr('ui-jp')]).then( function(){
				self[self.attr('ui-jp')].apply(self, options);
			// });
        });

        return lists;
	}

})(jQuery);

new Vue({
    el: '#kabooodle_app',
    data: {},
    methods: {
        disableOnClick: function(){
        }
    }
});

$(function () {

    // $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    //     var token;
    //     if (! options.crossDomain) {
    //         token = $('meta[name="token"]').attr('content');
    //         if (token) {
    //             jqXHR.setRequestHeader('X-CSRF-Token', token);
    //         }
    //     }
    //
    //     return jqXHR;
    // });

    // $.ajaxSetup({
    //     beforeSend: function (xhr) {
    //         xhr.setRequestHeader('Accept', 'application/json');
    //     },
    //     statusCode: {
    //         401: function () {
    //             window.location.href = '/';
    //         },
    //         403: function () {
    //             window.location.href = '/';
    //         }
    //     }
    // });

    $('form').submit(function() {
        var $form = $(this);
        $form.find(':submit').prop('disabled', true);
    });

    $('[data-toggle="tooltip"]').tooltip();
    // $('[ui-jp]').uiJp();
    $('.dropdown.dropdown-onhover').hover(function() {
        $(this).addClass('open active').find('.dropdown-menu').stop(true, true).show();
    }, function() {
        $(this).removeClass('open active').find('.dropdown-menu').stop(true, true).hide();
    });

    $('.float').keypress(function(event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
//# sourceMappingURL=app.js.map
