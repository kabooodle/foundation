<head>
    <meta charset="utf-8" />
    <title>@yield('page-title', 'Kabooodle')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="user_hash" content="{{ $_auth_token }}">
    <meta name="token" content="{{ csrf_token() }}">

    @push('header-styles')
    <link rel="stylesheet" href="{{ staticAsset('/assets/css/vendor.css') }}" type="text/css"  />
    <link rel="stylesheet" href="{{ staticAsset('/assets/css/app.css') }}?" type="text/css" />
    <link rel="stylesheet" href="{{ staticAsset('/assets/css/merchant.css') }}" type="text/css"/>
    @endpush

    <script type="text/javascript">
        const KABOOODLE_APP = window.KABOOODLE_APP || {};
        KABOOODLE_APP.currentUser = {!! $_current_user !!};
    </script>

    @push('header-scripts')
    <script src="{{ staticAsset('/assets/js/vendor.js') }}"></script>

    <script src="//js.pusher.com/3.2/pusher.min.js"></script>

    <script type="text/javascript">
        @if ($_current_user)

                Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="token"]').attr('content');
                Vue.http.headers.common['Authorization'] = "Bearer " + $('meta[name=user_hash]').attr("content");

        $(function(){
            document.addEventListener("turbolinks:request-start", function(event) {
                var xhr = event.data.xhr;
                xhr.setRequestHeader("Authorization", "Bearer "+$('meta[name=user_hash]').attr("content")+"");
            });

            $.ajaxPrefilter(function(options, originalOptions, xhr ) {
                if (options.url.toLowerCase().indexOf("amazonaws") <= 0) {
                    xhr.setRequestHeader("Authorization", "Bearer " + $('meta[name=user_hash]').attr("content") + "");
                }
            });

            $.ajaxSetup({
                async: true,
                headers: {
                    '_token': $('meta[name="token"]').attr('content'),
                    'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
                }
            });
        });

        {{--var pusher = new Pusher('{{ env('PUSHER_KEY') }}');--}}
        {{--var channel = pusher.subscribe('private_'+KABOOODLE_APP.currentUser.username);--}}
        {{--channel.bind('kabooodle.testevent', function(data) {--}}
            {{--var newCount = data.unreadNotificationsCount;--}}
            {{--$('#notify_total').addClass('warning').removeClass('hide').html(newCount);--}}
        {{--});--}}
        @endif
    </script>
    @endpush


    @stack('header-styles')

    @stack('header-scripts')
</head>