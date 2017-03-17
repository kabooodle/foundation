@extends('layouts.full', ['contentId' => 'merchant_listings_index'])


@section('body-content')

    @if($listings)
    <div class="box">
        <div class="box-header">
            <h4>Listings</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Scheduled For</th>
                        <th scope="col">Albums</th>
                        <th scope="col">Items</th>
                        <th scope="col">Sales</th>
                        <th scope="col">Pending</th>
                        <th scope="col">Views</th>
                        <th scope="col">Gross</th>
                        <th scope="col">Listing Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($listings as $listing)
                    @include('listings.partials._indexrow', ['listing' => $listing])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="onboard-card onboard_wrapper onboard-managelistings">
            <div class="onboard-body text-center">
                <h1 class="onboard-card-title">
                    Manage listings
                </h1>
                <h2 class="onboard-card-sub-title text-center m-b-3">
                    Inventory you've listed to facebook and flash sales will be shown on this page.
                    <br>
                    You'll be able to which sales generated the most revenue, views, claims, and more!
                    <br>
                    Need to adjust or remove a listing? Yup, you'll do that on this page too.
                </h2>
                <button class="btn btn-lg btn-grn m-b-2"><a href="{{ route('merchant.listings.create') }}">Ok, I'll go list some items!</a></button>
            </div>
        </div>
    @endif
@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listing-index-merchant.js') }}"></script>
<script>
    var channel = KABOOODLE_APP.pusher.subscribe('private.'+KABOOODLE_APP.env+'.listings.'+KABOOODLE_APP.currentUser.id);
    channel.bind('listing:updated', function(data) {
        let target_row = $('tr[data-id="'+data.id+'"]');
        if (target_row.length) {
            target_row.replaceWith(data.html);
        }
    });
</script>

@endpush