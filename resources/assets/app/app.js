new Vue({
    el: '#kabooodle_app',
    data: {},
    methods: {
        disableOnClick: function () {
        }
    }
});

function confirmModal(confirmCB, closeCB) {
    noty({
        text: 'Confirm that you wish to proceed with purchase.',
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
                addClass: 'btn btn-sm primary', text: 'Confirm Purchase', onClick: function ($noty) {
                if (typeof confirmCB === 'function') {
                    confirmCB();
                }
            }
            },
            {
                addClass: 'btn btn-link btn-sm', addId: 'noty_cancel', text: 'Cancel', onClick: function ($noty) {
                $noty.close();
                if (typeof closeCB === 'function') {
                    closeCB();
                }
            }
            }
        ]
    });
}

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

    $('form').submit(function () {
        var $form = $(this);
        $form.find(':submit').prop('disabled', true);
    });

    $('[data-toggle="tooltip"]').tooltip();
    // $('[ui-jp]').uiJp();
    $('.dropdown.dropdown-onhover').hover(function () {
        $(this).addClass('open active').find('.dropdown-menu').stop(true, true).show();
    }, function () {
        $(this).removeClass('open active').find('.dropdown-menu').stop(true, true).hide();
    });

    $('.float').keypress(function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });


});