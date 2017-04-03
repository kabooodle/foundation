@extends('layouts.full', ['contentId' => 'claims_index'])


@section('body-menu')
    <div class="clearfix">
        <div class="pull-left">
            <span class="inline btn-group-vertical _500" style="margin-top: 5px;">{{ rand(1,100) }} <span class="text-muted">Pending Claims</span></span>
            <span class="inline btn-group-vertical _500 m-l" style="margin-top: 5px;">{{ rand(1,100) }} <span class="text-muted">Total Sales</span></span>
        </div>
        <div class="pull-right">
            <button class="btn btn-sm white">Need some filter button(s) here!</button>
        </div>
    </div>
@endsection

@section('body-content')

    <div class="box white m-b-0">
        <div class="box-header">
            <h4>Claims</h4>
        </div>
    </div>
    <claims-index
            fetch_endpoint="{{ apiRoute('claims.index') }}"
            accept_endpoint="{{ apiRoute('claims.accept') }}"
            return_endpoint="{{ apiRoute('claims.return') }}"
            label_endpoint="{{ apiRoute('claims.label') }}"
    ></claims-index>

    {{--@include('inventory.claims.partials._actionmodal')--}}

    {{--@else--}}



    {{--@endif--}}
@endsection


@push('footer-scripts')
<script src="{{  staticAsset("/assets/js/claims-index.js") }}"></script>
@endpush