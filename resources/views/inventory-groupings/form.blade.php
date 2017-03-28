@extends('layouts.full', ['contentId' => 'inventory-groupings-management'])

@section('body-menu')
    {{--<div class="pull-right">--}}
        {{--<button class="btn primary btn-sm">Save Outfit</button>--}}
    {{--</div>--}}
@endsection


@push('header-styles')

@endpush

@section('body-content')
    <manager
        :edit="{{ isset($grouping) ? 1 : 0 }}"
        :edit-grouping="{{ isset($grouping) ? $grouping : '{}' }}"
        s3_bucket="{{ env('AWS_BUCKET') }}"
        s3_acl="public-read"
        s3_key_url="{{ apiRoute('api.files.sign') }}"
        inventory-groupings-endpoint="{{ apiRoute('inventory-groupings.index', [webUser()->username]) }}"
        inventory-endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
        inventory-groupings-index-route="{{ route('shop.outfits.index', [webUser()->username]) }}"
    ></manager>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/inventory-groupings-management.js') }}"></script>
@endpush