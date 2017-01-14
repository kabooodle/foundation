@extends('layouts.full')


@section('body-content')

    <div class="text-center m-b-3 ">
        <img src="/assets/images/513550806.jpg" width="100%">
        <h1 style="line-height: 50px" class="m-b-3 _300 m-t-2">Share {{ env('APP_NAME') }} and Earn!</h1>
        <h5 class="_400 text-muted" style="line-height: 30px;">For every friend you refer whom joins {{ env('APP_NAME') }}, we'll credit your account a free month, up to 6 months.
            For each additional referral, your name will be entered in our biannual drawing to win 4 tickets to Disneyland or a $500 gift card.</h5>
    </div>

    <div class="box padding">
            <div class="row">
                <div class="col-md-6">
                    <p class="m-b-0 p-b-0 text-center"><span id="link-text">http://kabooodle.dev/invite/{{ user()->username }}</span>
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


    @if(user()->referrals->count() > 0)
    <div class="padding white p-b-0">
        <div class="row">
            @foreach(user()->referrals as $referral)

                <div class="col-md-4">
                    <ul class="list p-b-0 b-a {{ $referral->subscribed('main') ? 'b-success' : null }}">
                        <li class="list-item">
                            <a href="http://kabooodle.dev/shop/jaketoolson" class="list-left">
                            <span class="w-40 avatar">
                              <img src="https://placekitten.com/g/32/32" alt="...">
                                                        </span>
                            </a>
                            <div class="list-body">
                                <div class="_500"><a href="">{!! $referral->name !!}</a></div>
                                <div class="text-muted">joined: {{ $referral->created_at->diffForHumans() }}</div>
                                <div class="text-muted text-sm">{{ $referral->subscribed('main') ? 'Qualified!' : 'not yet qualified' }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
    @else
        <p>No referrals yet!</p>
    @endif

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