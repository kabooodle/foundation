
@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="">
        <button
                type="button"
                @click.prevent="selectAllListables"
                class="btn white btn-sm">Select all
        </button>
        <button
                :class="selected.listables.length === 0 ? 'disabled' : null"
                :disabled="selected.listables.length === 0 ? true : false"
                type="button"
                @click.prevent="resetSelectedListables"
                class="btn white btn-sm">Unselect all
        </button>
    </div>
@endsection


@push('header-styles')
<style>
    /*@media only screen and (max-width: 959px) {*/
    /*.navbar-side {*/
    /*width: 100% !important;*/
    /*}*/
    /*}*/
    .img-thumb {
        position: relative;
    }
    .img-thumb img {
        border-radius: .25rem;
    }

    .img-thumb:hover img {
        opacity: .4;
    }

    .img-thumb .fa {
        display: none;
    }

    .img-thumb:hover .fa {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        margin-top: -6px;
        margin-left: -1px;
        color: #f77a99;
    }


    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link.active {
        background: #fff !important;
    }

    .nav-tabs .nav-link {
        background: #ccc !important;
        border-bottom: 0;
    }
    [v-cloak] { display: none; }
</style>

@endpush

@section('body-content')

    <listable-groupings
            listablegroupings_endpoint="{{ apiRoute('inventory.index', [webUser()->username]) }}"
    ></listable-groupings>

    {{--<onboard-card class="onboard-manageinventory" v-if="inventory_items.length == 0 && ! actions.refreshing_data">--}}
        {{--<template slot="title">No inventory to manage or list</template>--}}
        {{--<template slot="subtext">--}}
            {{--Once you've added inventory, you can list it to Facebook &amp; flash sales anytime!--}}
            {{--<br>--}}
            {{--Wish to edit an item? You would do that here too :)--}}
        {{--</template>--}}
        {{--<template slot="extra"><button class="btn btn-lg btn-grn m-b-2"><a href="{{ route('shop.inventory.create', [webUser()->username]) }}" >Got it! Take me to add inventory</a></button></template>--}}
    {{--</onboard-card>--}}

@endsection

@push('footer-scripts')
<script src="{{ staticAsset("/assets/js/listable-index.js") }}"></script>
@endpush