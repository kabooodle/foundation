@extends('layouts.full', ['contentId' => 'merchant_listings_detailed_items'])





@section('body-content')

    @include('listings.partials._listingbox', ['listing' => $listing])

    <div class="box">
        <div class="box-header">
            <h4>Listed Inventory Items</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body" data>
            <table class="table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Views</th>
                    <th scope="col">Watchers</th>
                    <th scope="col">Gross</th>
                    @if($listing->type <> Kabooodle\Models\Listings::TYPE_CUSTOM)
                    <th scope="col">
                        @if($listing->type == Kabooodle\Models\Listings::TYPE_FACEBOOK)
                        Facebook
                            @endif
                            Listing Status
                    </th>
                    @endif
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listing->listingItems as $item)
                    @include('listings.partials._detailedrow', ['item' => $item])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listing-detailed.js') }}"></script>
@endpush