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
});