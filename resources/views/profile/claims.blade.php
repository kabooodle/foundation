@extends('layouts.full', ['contentId' => 'claims_page'])

@section('body-content')
    <div class="box white m-b-0">
        <div class="box-header">
            <h4>Claims</h4>
        </div>
    </div>
    <claims-list
        fetch_endpoint="{{ apiRoute('users.claims.index', [webUser()->username]) }}"
    ></claims-list>
@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/claims.js') }}"></script>
@endpush
