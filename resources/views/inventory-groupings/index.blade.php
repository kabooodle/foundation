@extends('layouts.full', ['contentId' => 'manage_listables'])

@section('body-menu')
    <div class="pull-right">
        <a href="{{ route('shop.outfits.overview.index', [webUser()->username]) }}" class="btn white btn-sm"><i class="fa fa-th" aria-hidden="true"></i> Simple View</a>
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
                        <input type="text" name="name" v-model="search_filter" class="form-control" @keyup.enter="performSearch" placeholder="Search by outfit name">
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
                    api-url="{{ apiRoute('inventory-groupings.detailed.index', [webUser()->username]) }}"
                    pagination-path="data"
                    data-path="data.data"
                    :fields="columns"
                    :per-page="50"
                    :append-params="moreParams"
                    @vuetable:loaded="onLoaded"
                    @vuetable:loading="onLoading"
                    @vuetable:load-success="onLoadSuccess"
                    @vuetable:pagination-data="onPaginationData"
                >
                    <template slot="actions" scope="props">
                        <div class="clearfix pull-md-right">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" class="btn btn-xs white dropdown-toggle no-caret"><i aria-hidden="true" class="hidden-sm-down fa fa-ellipsis-h"></i>
                                    <span class="hidden-sm-up">Options</span>
                                </a>
                                <div class="dropdown-menu dropdown-over dropdown-menu-sm pull-xs-none dropdown-menu-right">
                                    <button
                                        type="button"
                                        @click="viewItemButtonClicked(props.rowData.slug, '{{ route('shop.outfits.show', [webUser()->username, 'K']) }}', $event)"
                                        class="dropdown-item">View</button>
                                    <button
                                        type="button"
                                        @click="editItemButtonClicked(props.rowData.slug, '{{ route('shop.outfits.edit', [webUser()->username, 'K']) }}', false, $event)"
                                        class="dropdown-item">Edit</button>
                                    <button
                                        type="button"
                                        @click="claimButtonClicked(props.rowData.slug, '{{ route('shop.outfits.show', [webUser()->username, 'K']) }}', $event)"
                                        class="dropdown-item">Claim</button>
                                    <div class="divider"></div>
                                    <button
                                        type="button"
                                        @click="archiveItem(props.rowData.id, '{{ apiRoute('listables.archive', ['K']) }}', $event)"
                                        class="dropdown-item">Archive</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </vuetable>

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
    <script src="{{ staticAsset("/assets/js/listables-detailed.js") }}"></script>
@endpush