<div class="box">
    <div class="box-header clearfix">
        <p class="pull-left m-b-0 m-t-0">
            <a class="text-primary" href="{{ route('merchant.listings.index') }}">@include('listings._listingtype', ['_type' => $listing->type, '_size' => 20]) {{ $listing->present()->listingParentName() }}</a>
        </p>
    </div>
    @if($listing->type <> Kabooodle\Models\Listings::TYPE_CUSTOM && $listing->scheduled_for)
    <div class="box-divider"></div>
    <div class="box-body">
        <p class="clearfix p-b-0 m-b-0">
            Scheduled For: {{ $listing->humanize($listing->scheduled_for) }}
            {{--<span class="pull-right">--}}
                {{--@if($listing->type == Kabooodle\Models\Listings::TYPE_FACEBOOK)--}}
                    {{--Facebook--}}
                {{--@endif--}}
                {{--listing status:--}}
                {{--{!! listingStatusHtml($listing->status) !!}--}}
            {{--</span>--}}
        </p>
    </div>
    @endif
</div>