@extends('layouts.full', ['contentId' => 'inventory-groupings'])

@section('body-menu')
    <div class="pull-right">
        <a href="{{ route('shop.outfits.index', [webUser()->username]) }}" class="btn white btn-sm"><i class="fa fa-th-list" aria-hidden="true"></i> Detail View</a>
    </div>
@endsection

@section('body-content')
    <inventory-groupings-display
        inventory-groupings-endpoint="{{ apiRoute('inventory-groupings.index', [webUser()->username]) }}"
        inventory-groupings-index-route="{{ route('shop.outfits.index', [webUser()->username]) }}"
    ></inventory-groupings-display>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset("/assets/js/inventory-groupings-simple.js") }}"></script>
@endpush