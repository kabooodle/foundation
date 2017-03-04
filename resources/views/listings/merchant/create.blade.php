@extends('layouts.full', ['contentId' => 'create_listings'])

@section('body-menu')

@endsection


@section('body-content')

            <create-listing
                    facebook_refresh_endpoint="{{ apiRoute('social.refresh') }}"
                    save_endpoint="{{ apiRoute('listings.store') }}"
                    :show_select_buttons="true"
                    endpoint="{{ apiRoute('listables.index', [webUser()->username]) }}"
            ></create-listing>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset("/assets/js/listing-create.js") }}"></script>
@endpush