@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')

@endsection


@push('header-styles')

@endpush

@section('body-content')
    <div id="inventory-groupings-management">
        <manager
            :edit="{{ isset($grouping) ? 1 : 0 }}"
            :edit-grouping="{{ isset($grouping) ? $grouping : '{}' }}"
            s3_key_url="{{ apiRoute('api.files.sign') }}"
            inventory-groupings-endpoint="{{ apiRoute('inventory-groupings.index', [webUser()->username]) }}"
            inventory-endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
            inventory-groupings-index-route="{{ route('shop.outfits.index', [webUser()->username]) }}"
        ></manager>
    </div>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/inventory-groupings-management.js') }}"></script>
@endpush