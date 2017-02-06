<div class="box" >
    <div class="box-body text-center dker">
        <h6 class="text-u-c m-a-0 m-t">{{ $_plan['name'] }}</h6>
        <h3 class="m-a-0">
            <sup>$</sup>
            <span class="text-2x" style="letter-spacing: -2px;">{{ isset($_price) ? $_price : $_plan['monthly']['price'] }}</span>
        </h3>
    </div>
    <div class="box-body p-b-0" style="height: 540px;">
        <img class="m-b-1" src="{{ $_plan['image'] }}" style="display: block; margin: auto;">
        @foreach($_plan['features'] as $feature)
            <p class="text-center">{!! $feature  !!}</p>
        @endforeach
    </div>
    @unless(isset($_hideButtons) or false)
    <div class="text-center p-a-md">
        @if(webUser()->currentSubscription() && !webUser()->currentSubscription()->cancelled() && webUser()->subscribedToPlan([$_plan['monthly']['id'], $_plan['annual']['id']], $_plan['subscription']))
            <a
                    href="{{ route('profile.subscription.cancel', [$_plan['monthly']['id']]) }}"
                    style=" margin: 0 2px 0 0"
                    class="btn  btn-lg danger">
                <strong>Cancel Subscription</strong>
                <small class="text-sm block">this cannot be undone!</small>
            </a>
        @else
            {{--{{ Form::open(['route' => ['profile.subscription.store', 'p=kabooodle_launch_plan']]) }}--}}
            <a href="{{ route('profile.subscription.show', [$_plan['monthly']['id']]) }}" style="width: 140px; margin: 0 2px 0 0" type="submit" class="btn  btn-lg white">
                <strong>${{ $_plan['monthly']['price'] }}</strong>
                <small class="text-sm block">monthly plan</small>
            </a>
            <a  href="{{ route('profile.subscription.show', [$_plan['annual']['id']]) }}" style="width: 140px; margin: 0 2px 0 0" type="submit" class="btn  btn-lg primary">
                <strong>${{ $_plan['annual']['price'] }}</strong>
                <small class="text-sm block">annual plan</small>
            </a>
            <div class="block">
                <span class="b" style="display: inline-block; width:140px;"></span>
                <span><small class="text-xs m-a-0 p-a-0">20% savings!</small></span>
            </div>
            {{--{{ Form::close() }}--}}
        @endif
    </div>
    @endunless
</div>