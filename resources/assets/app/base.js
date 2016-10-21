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