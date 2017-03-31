@extends('layouts.full', ['contentId' => 'claims_index'])

@section('body-content')


    <div class="box white">
        <div class="box-header">
            <h4>Claims</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <claims-index
                    fetch_endpoint="{{ apiRoute('claims.index') }}"
            ></claims-index>
        </div>
    </div>

    {{--@include('inventory.claims.partials._actionmodal')--}}

    {{--@else--}}

        {{--<onboard-card class="onboard-pendingclaims">--}}
            {{--<template slot="title">No pending claims</template>--}}
            {{--<template slot="subtext">                    When an item you have listed is claimed, it will be displayed on this page.--}}
                {{--<br>--}}
                {{--Decide which claims you wish to accept and treat as a completed sale,--}}
                {{--<br> or reject it, returning the item to your inventory.</template>--}}
        {{--</onboard-card>--}}

    {{--@endif--}}
@endsection


@push('footer-scripts')
<script src="{{  staticAsset("/assets/js/claims-index.js") }}"></script>
@endpush