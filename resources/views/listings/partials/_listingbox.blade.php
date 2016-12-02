<div class="box">
    <div class="box-body">
        <h6 class="clearfix p-b-0 m-b-0">{{ $listing->humanize($listing->scheduled_for) }} <span class="pull-right">{!! listingStatusHtml($listing->status) !!}</span></h6>
    </div>
</div>