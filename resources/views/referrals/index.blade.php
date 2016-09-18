@extends('layouts.full')


@section('body-content')

    <div class="text-center m-b-3 ">
        <img src="/assets/images/513550806.jpg" width="100%">
        <h1 style="line-height: 50px" class="m-b-3 _300 m-t-2">Get your merchant account for free for a year when you invite your friends to {{ env('APP_NAME') }}!</h1>
        <h5 class="_400 text-muted" style="line-height: 30px;">If you refer 10 friends, and they upgrade their accounts to basic, we'll give you the merchant account for free for a year! Need shipping labels? For every 5 friends whom join, we'll give you 100 labels too!</h5>
    </div>

    <div class="box padding">
            <div class="row">
                <div class="col-md-6">
                    <p class="m-b-0 p-b-0 text-center"><span id="link-text">http://kabooodle.dev/invite/3490eirofjk</span>
                        <button class="btn btn-xs white" data-clipboard-target="#link-text">
                            Copy Link
                        </button>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="m-b-0 p-b-0 text-center">Share to Facebook <span class="fb-share-button" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a></span>
                    </p>
                </div>
            </div>
    </div>


    <div class="padding white p-b-0">
        <div class="row">
            @foreach(user()->referrals as $referral)

                <div class="col-md-4">
                    <ul class="list p-b-0 b-a">
                        <li class="list-item">
                            <a href="http://kabooodle.dev/shop/jaketoolson" class="list-left">
                            <span class="w-40 avatar">
                              <img src="https://placekitten.com/g/32/32" alt="...">
                                                        </span>
                            </a>
                            <div class="list-body">
                                <div class="_500"><a href="http://kabooodle.dev/shop/jaketoolson">{!! $referral->name !!}</a></div>
                                <div class="text-muted">{{ $referral->created_at->diffForHumans() }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    @push('footer-scripts')
    <script>
        $('button').tooltip({
            trigger: 'click',
            placement: 'bottom'
        });

        function setTooltip(btn, message) {
            $(btn)
                    .attr('data-original-title', message)
                    .tooltip('show');
        }

        function hideTooltip(btn) {
            setTimeout(function() {
                $(btn).tooltip('hide');
            }, 1000);
        }

        var clipboard = new Clipboard('button');

        clipboard.on('success', function(e) {
            setTooltip(e.trigger, 'Copied!');
            hideTooltip(e.trigger);
            e.clearSelection();
        });
    </script>
    @endpush
@endsection