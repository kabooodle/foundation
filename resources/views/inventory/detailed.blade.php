@extends('layouts.full', ['contentId' => 'manage_inventory'])


@section('body-menu')
    <div class=" center-block text-center " >
        <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
                <input type="text" name="name" v-model="search_filter" class="form-control" @keyup.enter="performSearch" placeholder="Search by item name">
            </div>
        </div>
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
        <div class="box-body">
            <spinny v-if="actions.loading"></spinny>
                <vuetable ref="vuetable"
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

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/inventory-detailed.js') }}"></script>
@endpush