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

;
function moneyfy(n) {
    n = parseFloat(n);
    n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    if(isNaN(n)){
        return 0;
    }
    return n;
};

function getAllUrlParams(url) {

    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    var obj = {};

    if (queryString) {

        queryString = queryString.split('#')[0];

        var arr = queryString.split('&');

        for (var i=0; i<arr.length; i++) {
            var a = arr[i].split('=');
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function(v) {
                paramNum = v.slice(1,-1);
                return '';
            });

            var paramValue = typeof(a[1])==='undefined' ? true : a[1];

            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();

            if (obj[paramName]) {
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                if (typeof paramNum === 'undefined') {
                    obj[paramName].push(paramValue);
                }
                else {
                    obj[paramName][paramNum] = paramValue;
                }
            }
            else {
                obj[paramName] = paramValue;
            }
        }
    }

    return obj;
};

function randomAlphaStr(m) {
    var m = m || 9;
   var s = '',
        r = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    for (var i = 0; i < m; i++) {
        s += r.charAt(Math.floor(Math.random() * r.length));
    }
    return s;
};

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
};

function arrayUnique(array) {
    var a = array.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};
String.prototype.regexIndexOf = function(regex, startpos) {
    var indexOf = this.substring(startpos || 0).search(regex);
    return (indexOf >= 0) ? (indexOf + (startpos || 0)) : indexOf;
};

String.prototype.regexLastIndexOf = function(regex, startpos) {
    regex = (regex.global) ? regex : new RegExp(regex.source, "g" + (regex.ignoreCase ? "i" : "") + (regex.multiLine ? "m" : ""));
    if(typeof (startpos) == "undefined") {
        startpos = this.length;
    } else if(startpos < 0) {
        startpos = 0;
    }
    var stringToWorkWith = this.substring(0, startpos + 1);
    var lastIndexOf = -1;
    var nextStop = 0;
    while((result = regex.exec(stringToWorkWith)) != null) {
        lastIndexOf = result.index;
        regex.lastIndex = ++nextStop;
    }
    return lastIndexOf;
};

if (!Array.prototype.includes) {
    Array.prototype.includes = function(searchElement /*, fromIndex*/) {
        'use strict';
        if (this == null) {
            throw new TypeError('Array.prototype.includes called on null or undefined');
        }

        var O = Object(this);
        var len = parseInt(O.length, 10) || 0;
        if (len === 0) {
            return false;
        }
        var n = parseInt(arguments[1], 10) || 0;
        var k;
        if (n >= 0) {
            k = n;
        } else {
            k = len + n;
            if (k < 0) {k = 0;}
        }
        var currentElement;
        while (k < len) {
            currentElement = O[k];
            if (searchElement === currentElement ||
                (searchElement !== searchElement && currentElement !== currentElement)) { // NaN !== NaN
                return true;
            }
            k++;
        }
        return false;
    };
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (item, start) {
        start = start || 0;
        for( ; start < this.length; start++) {
            if (this[start] === item) {
                return start;
            }
        }
        return -1;
    };
}

if (!Array.prototype.filter) {
    Array.prototype.filter = function (iterator, context) {
        var arr = [];
        var i;
        for (i = 0; i < this.length; i += 1) {
            if (iterator.call(context, this[i])) {
                arr.push(this[i]);
            }
        }
        return arr;
    };
}
if (!Array.prototype.reject) {
    Array.prototype.reject = function (iterator, context) {
        return this.filter(function (item) {
            return !iterator.call(context, item);
        });
    };
}

function confirmModal(confirmCB, closeCB, options) {
    var defaults = {
        text: '<h6>Are you sure you want to continue?</h6>',
        layout: 'center',
        theme: 'relax',
        type: 'alert',
        modal: true,
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'linear',
            speed: 1
        },
        timeout: 4000,
        buttons: [
            {
                addClass: 'btn btn-sm primary noty-btn-primary noty-btn', text: 'Continue', onClick: function ($noty) {
                if (typeof confirmCB === 'function') {
                    confirmCB($noty);
                }
            }
            },
            {
                addClass: 'btn white btn-sm noty-btn-cancel noty-btn', addId: 'noty_cancel', text: 'Cancel', onClick: function ($noty) {
                $noty.close();
                if (typeof closeCB === 'function') {
                    closeCB($noty);
                }
            }
            }
        ]
    };

    options = $.extend({}, defaults, options);

    noty(options);
};

function notify(options){
    var defaults = {
        text: '',
        layout: 'top',
        theme: 'relax',
        type: 'error',
        animation: {
            open: 'animated bounceInDown',
            close: 'animated bounceOutUp'
        },
        timeout: 4000,
        closeWith: ['button','click']
    };

    options = $.extend({}, defaults, options);

    noty(options);
};

function spinny(size){
    if(typeof size === 'undefined') {
        size = 14;
    }
    return ' <img style="margin:-2px 0 0 0; padding:0;" height="'+size+'" width="'+size+'" src="/assets/images/icons/ring-alt.gif">';
};

function snakeToCamel(s){
    return s.replace(/(\-\w)/g, function(m){return m[1].toUpperCase();});
};


$(function () {

    $(document).trigger("enhance.tablesaw");

    $.extend( {
        findFirst: function( elems, validateCb ){
            var i;
            for( i=0 ; i < elems.length ; ++i ) {
                if( validateCb( elems[i], i ) )
                    return elems[i];
            }
            return undefined;
        }
    } );

    $(document).on('click', '[data-toggle="lightbox"]', function (e) {
        e.preventDefault();
        $(this).ekkoLightbox();
    });

    $('form').submit(function (e) {
        var $form = $(this),
            btn = $form.find(':submit'),
            btnHtml = btn.html();

        setTimeout(function(){
            if(! e.isDefaultPrevented()) {
                btn.prop('disabled', true).html(btnHtml + ' <i class="fa fa-spin fa-spinner"></i>');
            }
        },0);
    });

    $('[data-toggle="multiselect"]').multiselect({
        buttonClass: 'btn white btn-sm',
        maxHeight: 300,
        buttonWidth: '100%',
        enableFiltering: false,
        disableIfEmpty: true,
        numberDisplayed: 1
    });
    $('[data-ride="carousel"]').carousel({
        interval : false
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('[data-scrollable="scrollable-y"]').perfectScrollbar({
        suppressScrollX: true
    });

    $('[data-scrollable="scrollable-x"]').perfectScrollbar({
        suppressScrollY: true
    });

    $('[data-scrollable="scrollable"]').perfectScrollbar();



    $('.dropdown.dropdown-onhover').hover(function () {
        $(this).addClass('open active').find('.dropdown-menu').stop(true, true).show();
    }, function () {
        $(this).removeClass('open active').find('.dropdown-menu').stop(true, true).hide();
    });

    $('.float').keypress(function (e) {
        if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
            e.preventDefault();
        }
    });
});
//# sourceMappingURL=base.js.map
