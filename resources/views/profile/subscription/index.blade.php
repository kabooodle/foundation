@extends('profile.settingstemplate')


@section('settings-content')

    <div class="box">
        <div class="box-header">
            <h2>Subscription Plan</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body clearfix">
            <p class="m-b-0">Using {{ env('APP_NAME') }} to browse items and submit claims is free.  If you wish to have access to merchant inventory tools, including a b c, we offer various subscription plans.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row no-gutter">
                <div class="col-sm-4">
                    <div class="box">
                        <div class="box-body text-center lter success">
                            <h6 class="text-u-c m-a-0 m-t">Monthly</h6>
                            <h3 class="m-a-0">
                                <sup>$</sup>
                                <span class="text-2x">5</span>
                            </h3>
                        </div>
                        <div class="box-body p-b-0">
                            <p class="text-center m-b-0">Cheers to 2016! Only available through 2016!</p>
                        </div>
                        <div class="text-center p-a-md">
                            @if(user()->subscribedToPlan('kabooodle_launch_plan', 'main'))
                                <button type="button" disabled class="btn disabled btn-block btn-lg white">Current Plan</button>
                            @else
                            {{ Form::open(['route' => ['profile.subscription.store', 'p=kabooodle_launch_plan']]) }}
                            <button type="submit" class="btn btn-block btn-lg warning">Purchase</button>
                            {{ Form::close() }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 text-muted">
                    <div class="box">
                        <div class="box-body text-center dker">
                            <h6 class="text-u-c m-a-0 m-t">Monthly</h6>
                            <h3 class="m-a-0 m-v">
                                <sup>$</sup>
                                <span class="text-2x " style="letter-spacing: -4px;">10</span>
                            </h3>
                        </div>
                        <div class="box-body p-b-0">
                            <p class="text-center m-b-0">I want to start listing items in my inventory!</p>
                        </div>
                        <div class="text-center p-a-md">
                            <button type="button" disabled class="disabled btn btn-block btn-lg white">Unavailable</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 text-muted">
                    <div class="box">
                        <div class="box-body text-center dker">
                            <h6 class="text-u-c m-a-0 m-t">Yearly</h6>
                            <h3 class="m-a-0 m-v">
                                <sup>$</sup>
                                <span class="text-2x" style="letter-spacing: -2px;">99</span>
                            </h3>
                        </div>
                        <div class="box-body p-b-0">
                            <p class="text-center m-b-0">over 20% discount off the monthly rate</p>
                        </div>
                        <div class="text-center p-a-md">
                            <button type="button" disabled class="btn btn-block btn-lg disabled white">Unavailable</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(user()->subscribedToPlan('kabooodle_launch_plan', 'main'))
    <div class="m-t-2">
        <button type="button"  id="cancel-subscription" class="btn-link text-danger">Cancel Subscription</button>
    </div>
    @endif

@endsection


@push('footer-scripts')

@if(user()->subscribedToPlan('kabooodle_launch_plan', 'main'))
<script>
$(function(){
   var formCancelEl = $('#cancel-subscription');
    formCancelEl.click(function(e){
        e.preventDefault();
        var $that = $(this);
        $.ajax({
            url: '{{ route('profile.subscription.destroy') }}',
            type: 'DELETE'
        });
//        $that.addClass('disabled').prop('disabled', true);
        //        formCancelEl.submit();
    });
});
</script>
@endif
@endpush