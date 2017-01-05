@extends('layouts.full', ['contentId' => 'user_listings_index'])

@section('body-menu')
    <div class="pull-left">
        <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter listings</button>
    </div>
@endsection


@section('body-content')
    <user-listings
            :user="{{ $user->toJson()  }}"
            listing_endpoint="{{ apiRoute('users.listings.show', [$user->username, '::UUID::']) }}"
            listings_endpoint="{{ apiRoute('users.listings.index', [$user->username]) }}"
    ></user-listings>
@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/user-listings-index.js') }}"></script>
@endpush
