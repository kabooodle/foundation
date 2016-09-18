
<head>
    <meta charset="utf-8" />
    <title>@yield('page-title', 'Kabooodle')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="user_hash" content="{{ $_auth_token }}">
    <meta name="token" content="{{ csrf_token() }}">

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/vendor.css?{{ getAppVersion() }}" type="text/css"  />
    <link rel="stylesheet" href="/assets/css/app.css?{{ getAppVersion() }}" type="text/css" />
    @endpush

    @push('header-scripts')
    <script src="/assets/js/vendor.js?{{ getAppVersion() }}"></script>
    <script src="/assets/js/merchant.js?{{ getAppVersion() }}"></script>
    <script src="//js.pusher.com/3.2/pusher.min.js"></script>
    @endpush

    @stack('header-styles')

    @stack('header-scripts')
    <script type="text/javascript">
        var KABOOODLE_APP = window.KABOOODLE_APP || {};
        KABOOODLE_APP.currentUser = {!! $_current_user ? $_current_user->toJson() : 'null' !!};

        @if ($_current_user)

                Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="token"]').attr('content');

        $(function(){
            document.addEventListener("turbolinks:request-start", function(event) {
                var xhr = event.data.xhr;
                xhr.setRequestHeader("Authorization", "Bearer "+$('meta[name=user_hash]').attr("content")+"");
            });

            $.ajaxPrefilter(function(options, originalOptions, xhr ) {
                xhr.setRequestHeader("Authorization", "Bearer "+$('meta[name=user_hash]').attr("content")+"");
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
        });

        {{--var pusher = new Pusher('{{ env('PUSHER_KEY') }}');--}}
        {{--var channel = pusher.subscribe('private_{{ $_current_user->username }}');--}}
        {{--channel.bind('kabooodle.testevent', function(data) {--}}
            {{--var newCount = data.unreadNotificationsCount;--}}
            {{--$('#notify_total').addClass('warning').removeClass('hide').html(newCount);--}}
        {{--});--}}
        @endif
    </script>

</head>