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

const spinny = ' <img style="margin:-1px 0 0 0; padding:0;" height="14" width="14" src="/assets/images/icons/ring-alt.gif">';

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