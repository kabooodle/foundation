@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="text-center center-block">
        <a
                href="{{ route('shop.inventory.create', [webUser()->username]) }}"
                class="btn primary btn-sm">Add Inventory
        </a>
        <a
                href="{{ route('shop.outfits.create', [webUser()->username]) }}"
                class="btn primary btn-sm">Create Outfits
        </a>
        <a
                href="{{ route('merchant.listings.create') }}"
                class="btn primary btn-sm">Create Sales Listings
        </a>
    </div>
@endsection

@section('body-content')
    <div id="inventory-groupings">
        <inventory-groupings-display
            inventory-groupings-endpoint="{{ apiRoute('inventory-groupings.index', [webUser()->username]) }}"
        ></inventory-groupings-display>
    </div>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset("/assets/js/inventory-groupings.js") }}"></script>
@endpush