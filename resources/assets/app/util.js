/**
 * jQuery serializeObject
 */
(function(root, factory) {

    // AMD
    if (typeof define === "function" && define.amd) {
        define(["exports", "jquery"], function(exports, $) {
            return factory(exports, $);
        });
    }

    // CommonJS
    else if (typeof exports !== "undefined") {
        var $ = require("jquery");
        factory(exports, $);
    }

    // Browser
    else {
        factory(root, (root.jQuery || root.Zepto || root.ender || root.$));
    }

}(this, function(exports, $) {

    var patterns = {
        validate: /^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
        key:      /[a-z0-9_]+|(?=\[\])/gi,
        push:     /^$/,
        fixed:    /^\d+$/,
        named:    /^[a-z0-9_]+$/i
    };

    function FormSerializer(helper, $form) {

        // private variables
        var data     = {},
            pushes   = {};

        // private API
        function build(base, key, value) {
            if (typeof value === 'undefined' || value === null) {
                return base;
            }
            base[key] = value;
            return base;
        }

        function makeObject(root, value) {

            var keys = root.match(patterns.key), k;

            // nest, nest, ..., nest
            while ((k = keys.pop()) !== undefined) {
                // foo[]
                if (patterns.push.test(k)) {
                    var idx = incrementPush(root.replace(/\[\]$/, ''));
                    value = build([], idx, value);
                }

                // foo[n]
                else if (patterns.fixed.test(k)) {
                    value = build([], k, value);
                }

                // foo; foo[bar]
                else if (patterns.named.test(k)) {
                    value = build({}, k, value);
                }
            }

            return value;
        }

        function incrementPush(key) {
            if (pushes[key] === undefined) {
                pushes[key] = 0;
            }
            return pushes[key]++;
        }

        function encode(pair) {
            switch ($('[name="' + pair.name + '"]', $form).attr("type")) {
                case "checkbox":
                    return pair.value === "on" ? true : pair.value;
                default:
                    return pair.value;
            }
        }

        function addPair(pair) {
            if (!patterns.validate.test(pair.name)) return this;
            var obj = makeObject(pair.name, encode(pair));
            data = helper.extend(true, data, obj);
            return this;
        }

        function addPairs(pairs) {
            if (!helper.isArray(pairs)) {
                throw new Error("formSerializer.addPairs expects an Array");
            }
            for (var i=0, len=pairs.length; i<len; i++) {
                this.addPair(pairs[i]);
            }
            return this;
        }

        function serialize() {
            return data;
        }

        function serializeJSON() {
            return JSON.stringify(serialize());
        }

        // public API
        this.addPair = addPair;
        this.addPairs = addPairs;
        this.serialize = serialize;
        this.serializeJSON = serializeJSON;
    }

    FormSerializer.patterns = patterns;

    FormSerializer.serializeObject = function serializeObject() {
        return new FormSerializer($, this).
        addPairs(this.serializeArray()).
        serialize();
    };

    FormSerializer.serializeJSON = function serializeJSON() {
        return new FormSerializer($, this).
        addPairs(this.serializeArray()).
        serializeJSON();
    };

    if (typeof $.fn !== "undefined") {
        $.fn.serializeObject = FormSerializer.serializeObject;
        $.fn.serializeJSON   = FormSerializer.serializeJSON;
    }

    exports.FormSerializer = FormSerializer;

    return FormSerializer;
}));

function getAllUrlParams(url) {

    // get query string from url (optional) or window
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    // we'll store the parameters here
    var obj = {};

    // if query string exists
    if (queryString) {

        // stuff after # is not part of query string, so get rid of it
        queryString = queryString.split('#')[0];

        // split our query string into its component parts
        var arr = queryString.split('&');

        for (var i=0; i<arr.length; i++) {
            // separate the keys and the values
            var a = arr[i].split('=');

            // in case params look like: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function(v) {
                paramNum = v.slice(1,-1);
                return '';
            });

            // set parameter value (use 'true' if empty)
            var paramValue = typeof(a[1])==='undefined' ? true : a[1];

            // (optional) keep case consistent
            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();

            // if parameter name already exists
            if (obj[paramName]) {
                // convert value to array (if still string)
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // if no array index number specified...
                if (typeof paramNum === 'undefined') {
                    // put the value on the end of the array
                    obj[paramName].push(paramValue);
                }
                // if array index number specified...
                else {
                    // put the value at that index number
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // if param name doesn't exist yet, set it
            else {
                obj[paramName] = paramValue;
            }
        }
    }

    return obj;
}

function randomAlphaStr(m) {
    var m = m || 9;
    s = '',
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
        timeout: 4000,
        closeWith: ['button','click']
    };

    options = $.extend({}, defaults, options);

    noty(options);
}

function spinny(size){
    if(typeof size === 'undefined') {
        size = 14;
    }
    return ' <img style="margin:-2px 0 0 0; padding:0;" height="'+size+'" width="'+size+'" src="/assets/images/icons/ring-alt.gif">';
};

$(function () {

    // $.fn.datetimepicker.defaults = {
    //     format: "MM/DD/YYYY",
    //     icons: {
    //         up: 'fa fa-chevron-up',
    //         down: 'fa fa-chevron-down',
    //         previous: 'fa fa-chevron-left',
    //         next: 'fa fa-chevron-right'
    //     }
    // };
    //
    // $.fn.datetimepicker = function (options) {
    //     var settings = $.extend($.datetimepicker.defaults, options);
    // };

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
        numberDisplayed: 1,
        // enableCaseInsensitiveFiltering: true,
        // templates: {
            // filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
            // filterClearBtn: '<span class="input-group-btn"><button style="padding-left: 6px; padding-right: 6px;" class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times-circle"></i></button></span>',
            // li: '<li><a tabindex="0" class="dropdown-item"><label></label></a></li>'
        // }
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