@extends('layouts.full', ['contentId' => 'manage_inventory'])


@section('body-menu')
    <div class="pull-right">
        {{--<a--}}
                {{--href="{{ route('shop.inventory.create', [webUser()->username]) }}"--}}
                {{--class="btn primary btn-sm">Add Inventory--}}
        {{--</a>--}}
        {{--<a--}}
                {{--href="{{ route('shop.outfits.create', [webUser()->username]) }}"--}}
                {{--class="btn primary btn-sm">Create Outfits--}}
        {{--</a>--}}
        {{--<a--}}
                {{--href="{{ route('merchant.listings.create') }}"--}}
                {{--class="btn primary btn-sm">Create Sales Listings--}}
        {{--</a>--}}
        <button class="btn white btn-sm">Download</button>
        <a href="{{ route('shop.inventory.overview.show', [webUser()->username]) }}" class="btn white btn-sm"><i class="fa fa-object-group" aria-hidden="true"></i> Simple View</a>
    </div>
@endsection

@section('body-content')

    <detailed-totals
            v-if="actions.loaded"
            :gross="totals.gross"
            :pageviews="totals.pageviews"
            :qty_on_hand="totals.qty_on_hand"
            :accepted_sales="totals.accepted_sales"
            :pending_sales="totals.pending_sales"
    ></detailed-totals>



    <div class="box white">
        <div class="box-header">
            <div class=" center-block text-center " >
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-3">
                        <input type="text" name="name" v-model="search_filter" class="form-control" @keyup.enter="performSearch" placeholder="Search by item name">
                    </div>
                </div>
            </div>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <spinny class="text-center center center-block" size="100" v-if="actions.loading"></spinny>
            <div class="vuetable-wrapper">
                <vuetable ref="vuetable"
                          wrapper-class="vuetable-wrapper"
                          api-url="{{ apiRoute('inventory.detailed.index', [webUser()->username]) }}"
                          pagination-path="data"
                          data-path="data.data"
                          :fields="columns"
                          :per-page="50"
                          :append-params="moreParams"
                          @vuetable:loaded="onLoaded"
                          @vuetable:loading="onLoading"
                          @vuetable:load-success="onLoadSuccess"
                          @vuetable:pagination-data="onPaginationData"
                ></vuetable>

                <div class="vuetable-pagination">
                    <vuetable-pagination-info
                            ref="paginationInfo"
                            info-class="pagination-info"
                    ></vuetable-pagination-info>
                    <vuetable-pagination
                            ref="pagination"
                            :css="css.pagination"
                            :icons="css.icons"
                            @vuetable-pagination:change-page="onChangePage"
                    ></vuetable-pagination>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/inventory-detailed.js') }}"></script>
@endpush