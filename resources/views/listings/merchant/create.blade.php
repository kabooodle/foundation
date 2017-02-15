@extends('layouts.full', ['contentId' => 'create_listings'])

@section('body-menu')

@endsection


@section('body-content')

            <create-listing
                    :show_select_buttons="true"
                    endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
            ></create-listing>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset("/assets/js/listing-create.js") }}"></script>
@endpush