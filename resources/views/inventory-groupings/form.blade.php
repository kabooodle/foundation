@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')

@endsection


@push('header-styles')

@endpush

@section('body-content')
    <div id="inventory-groupings-management">
        <manager
            s3_key_url="{{ apiRoute('api.files.sign') }}"
            save-inventory-groupings-endpoint="{{ apiRoute('inventory-groupings.index', [webUser()->username]) }}"
            get-inventory-endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
        ></manager>
    </div>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/inventory-groupings-management.js') }}"></script>
@endpush