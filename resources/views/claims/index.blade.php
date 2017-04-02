@extends('layouts.full', ['contentId' => 'claims_index'])

@section('body-content')


    <div class="box white m-b-0">
        <div class="box-header">
            <h4>Claims</h4>
        </div>
    </div>
    <claims-index
            fetch_endpoint="{{ apiRoute('claims.index') }}"
    ></claims-index>

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