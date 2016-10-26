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

function randomAlphaStr(m) {
    var m = m || 9;
    s = '',
        r = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    for (var i = 0; i < m; i++) {
        s += r.charAt(Math.floor(Math.random() * r.length));
    }
    return s;
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
}
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
    // augment the Array prototype with an indexOf that conforms
    // to ECMAScript5
    //   item - this is the object we're looking for
    //   start - this is where to start looking
    // returns the index of the item if found, otherwise -1
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
    // augment the Array prototype with a filter() that conforms
    // to ECMAScript5
    //   iterator - this function is called for each item, if it return
    //       a truthy value, that item is added to the returned array
    //   context - this is optional context to call the iterator. 'this'
    //       inside the iterator will be set to context.
    // returns an array with only items for which the iterator returned
    //     a truthy value
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
    // augment the Array prototype with a reject() that is the opposite
    // of filter().
    //   iterator - this function is called for each item, if it return
    //       a truthy value, that item is not added to the returned array
    //   context - this is optional context to call the iterator. 'this'
    //       inside the iterator will be set to context.
    // returns an array with only items for which the iterator did not
    //     return a truthy value
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
        timeout: 9000,
        buttons: [
            {
                addClass: 'btn btn-sm primary', text: 'Continue', onClick: function ($noty) {
                if (typeof confirmCB === 'function') {
                    confirmCB($noty);
                }
            }
            },
            {
                addClass: 'btn white btn-sm', addId: 'noty_cancel', text: 'Cancel', onClick: function ($noty) {
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
}

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
        timeout: 9000,
        closeWith: ['button','click']
    };

    options = $.extend({}, defaults, options);

    noty(options);
}

$(function () {

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
        enableFiltering: true,
        disableIfEmpty: true,
        enableCaseInsensitiveFiltering: true,
        templates: {
            filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
            filterClearBtn: '<span class="input-group-btn"><button style="padding-left: 6px; padding-right: 6px;" class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times-circle"></i></button></span>',
            li: '<li><a tabindex="0" class="dropdown-item"><label></label></a></li>'
        }
    });
    $('[data-ride="carousel"]').carousel({
        interval : false
    });
    $('[data-toggle="tooltip"]').tooltip();
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
;
/**
 *
 */
(function($, window, document, undefined){

    /**
     *
     * @param element
     * @param options
     * @returns {S3Uploader}
     * @constructor
     */
    function S3Uploader(element, options) {
        this.element = element;
        this.$element = $(element);
        this.options = $.extend({}, $.fn.s3uploader.defaults, options);
        this.options = $.extend({}, this.options, this._parseHtmlDataAttributes(this.$element));
        this.jqXHRCollection = [];
        this.templateElements = {
            progress_container: '.js-fileupload-progress',
            progress_bar: '.progress',
            add_file_button: '.fileinput-button',
            file_upload_target: '.js-s3_fileupload',
            cancel_button: '.js-cancel_button',
            showExtendedBool: true
        };
        this._defaults = $.fn.s3uploader.defaults;
        this.init();
    }

    /**
     *
     * @type {{init: Function, setTemplate: Function, initFileUpload: Function, setProgress: Function, buttonToggler: Function, throwException: Function, log: Function, _parseHtmlDataAttributes: Function}}
     */
    S3Uploader.prototype = {
        /**
         *
         * @returns {S3Uploader}
         * @private
         */
        init: function(){
            if (!$.fn.fileupload) {
                this.throwException('missing-dependency', 'fileupload plugin required.');
            }
            this.setTemplate(this.options.templateEl);
            this.initFileUpload();
            return this;
        },
        /**
         *
         */
        setTemplate: function($el){
            this.$element.html(
                $el ? this.$element.parent().find($el) : Template.getTemplate(this.options.multiple, this.options.button_name, this.options.extended_upload_info)
            , true);
        },
        /**
         *
         */
        initFileUpload: function(){
            var that = this;
            var fileUpload = {
                url: "https://" + that.options.s3_bucket + ".s3.amazonaws.com",
                dataType: "xml",
                type: "POST",
                dropZone: that.options.drop_zone,
                add: function (e, data) {
                    //since we are overriding the add function, in order for image-resize to work we must call the parent add()
                    $.blueimp.fileupload.prototype.options.add.call(this, e, data);
                    if(!that.options.on_file_add(e, data)){
                        return false;
                    }
                    that.buttonToggler(true);
                    that.$element.find(that.templateElements.progress_container).show();
                    var hash = Math.random().toString(36).substr(2, 5);
                    var timestamp = Math.floor(new Date().getTime() / 1000);
                    var ajaxData = that.options.s3_key_payload;
                    ajaxData.filename = timestamp + '_' + data.files[0].name;
                    that.jqXHRCollection.push($.ajax({
                        url: that.options.s3_key_url,
                        dataType: 'JSON',
                        type: 'GET',
                        data: ajaxData,
                        success: function (response) {
                            $(document).trigger('s3uploader.s3_key_retrieved', response);
                            that.log('api.files.s3key: done', response);
                            data.formData = {
                                AWSAccessKeyId:         response.data.AWSAccessKeyId,
                                acl:                    response.data.acl,
                                key:                    response.data.key,
                                policy:                 response.data.policy,
                                success_action_status:  201,
                                signature:              response.data.signature
                            };
                            that.options.response = response;
                            that.options.file = data.files[0];
                            that.jqXHRCollection.push(data.submit());
                        },
                        fail: function (e, data, error) {
                            that.throwException(e.responseText, error);
                            that.log('api.files.s3key: fail', error);
                            that.buttonToggler(false);
                        }
                    }));
                },
                formData: {},
                success: function (data, textStatus, jqXHR) {
                    that.options.on_s3_upload(data, textStatus, jqXHR);
                },
                done: function (e, data) {
                    console.log('done uploading files.');
                },
                fail: function(e, data, error){
                    that.throwException(e.responseText, error);
                },
                always: function() {
                },
                progress: function (e, data) {
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    var progress = Math.floor(data.loaded / data.total * 100);
                    if (data.context) {
                        that.setProgress(progress);
                    }
                },
                stop: function () {
                    that.resetProgress();
                    that.buttonToggler(false);
                }
            };
            fileUpload = $.extend(fileUpload, this.options.fileupload_options);
            that.$element.find(that.templateElements.file_upload_target).fileupload(fileUpload);
            $(this.templateElements.cancel_button).on('click', $.proxy(function(e){
                that = this;
                that.cancelAll();
            }, that));
        },
        /**
         *
         * @param percent
         */
        setProgress: function(percent) {
            var that = this;
            that.$element.find(that.templateElements.progress_bar)
                .attr('aria-valuenow', percent).children().first()
                .css('width', percent + '%');
        },

        /**
         *
         */
        resetProgress: function() {
            var that = this;
            setTimeout(function(){
                that.$element.find(that.templateElements.progress_bar)
                    .attr('aria-valuenow', 5).children().first()
                    .css('width', '5%');
            }, 1000);
        },
        /**
         *
         * @param enable
         */
        buttonToggler: function(enable) {
            var that = this;
            var addFileButton = that.$element.find(that.templateElements.add_file_button);
            var cancelButton = that.$element.find(that.templateElements.cancel_button);
            if(enable) {
                addFileButton.hide();
                cancelButton.show();
            } else {
                cancelButton.hide();
                addFileButton.show();
            }
        },
        cancelAll: function(){
            var that = this;
            $.each(that.jqXHRCollection, function(key, jqXHR){
                jqXHR.abort();
            });
            that.buttonToggler(false);
        },
        /**
         *
         * @param exception
         * @param error
         */
        throwException: function(exception, error){
            if(typeof Bugsnag != 'undefined') {
                Bugsnag.notify("S3Uploader", exception);
            }
            this.log(exception + ' exception:', error);
        },
        /**
         *
         * @param title
         * @param data
         */
        log: function(title, data) {
            if(this.options.debug) {
                console.log('S3Uploader: ' + title);
                console.log(data);
            }
        },
        /**
         *
         * @param el
         * @returns {Array}
         * @private
         */
        _parseHtmlDataAttributes: function(el) {
            var keys = [],
                elDataAttributes = el.data();
            for (var key in this.options) {
                if (this.options.hasOwnProperty(key) && elDataAttributes.hasOwnProperty(key)) {
                    keys[key] = elDataAttributes[key];
                }
            }
            return keys;
        }
    };

    var Template = {
        /**
         * @param multipleBool
         * @param customButtonName
         * @param showExtendedBool
         * @returns {string}
         */
        getTemplate: function(multipleBool, customButtonName, showExtendedBool){
            var name = 'file';
            var multiple = '';
            var buttonName = 'Add File';
            if(multipleBool) {
                name = 'file';
                multiple = ' multiple';
                buttonName = 'Add Files';
            }
            if(customButtonName) {
                buttonName = customButtonName;
            }
            var template = '' +
                '<div class="row">' +
                '    <div class="col-sm-4">' +
                '        <span class="btn btn-primary btn-sm fileinput-button">' +
                '            <i class="fa fa-plus"></i>&nbsp;' +
                             buttonName +
                '            <input type="file" name="' + name + '" class="js-s3_fileupload" ' + multiple + '>' +
                '        </span>' +
                '        <span class="btn btn-danger js-cancel_button btn-sm" style="display:none;">' +
                '            <i class="fa fa-spin fa-spinner"></i>' +
                '            Cancel' +
                '        </span>' +
                '    </div>' +
                '    <div class="col-sm-8">' +
                '        <div class="js-fileupload-progress fileupload-progress" style="display:none;">' +
                '            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">' +
                '                <div class="progress-bar progress-bar-success" style="width:0%;"></div>' +
                '            </div>';
            if(showExtendedBool) {
                template += '<div class="progress-extended">&nbsp;</div>';
            }
            template += '' +
                '        </div>' +
                '    </div>' +
                '</div>';
            return template;
        }
    };

    /**
     *
     * @param options
     * @returns {*}
     */
    $.fn.s3uploader = function(options){
        return this.each(function(){
            // console.log('new uploader init');
            return new S3Uploader(this, options);
        });
    };
    $.fn.s3uploader.defaults = {
        // required, can use $.fn.s3uploader.setDefaults({});
        s3_key_url: '',
        s3_bucket: '',
        // optional
        s3_key_payload: {},
        fileupload_options: {},
        multiple: false,
        debug: false,
        optional_s3_folder: '',
        video_tags: [],
        extended_upload_info: true,
        button_name: '',
        drop_zone: '',
        templateEl : null,
        maxChunkSize: 10000000,
        on_file_add: function (element, data) {},
        on_file_saved: function ($element, data) {},
        on_s3_upload: function (data) {}
    };
    $.fn.s3uploader.setDefaults = function(options){
        $.each(options, function(key, val){
            $.fn.s3uploader.defaults[ key ] = val;
        });
    };

})(jQuery, window, document);
//# sourceMappingURL=base.js.map
