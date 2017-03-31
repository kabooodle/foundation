@extends('layouts.full', ['contentId' => 'manage_listables'])


@section('body-menu')
    <div class="pull-left">

    </div>
    <div class="pull-right">
        <a href="{{ route('shop.inventory.archive.index', [webuser()->username]) }}" class="btn white btn-sm">Archived Inventory</a>
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
                    <div class="col-sm-3">
                        <button
                                @click="bulkDelete('{{ apiRoute('inventory.archive.bulk') }}')"
                                v-if="selectedTo.length"
                                type="button"
                                class="pull-left btn white">Bulk Archive (@{{ selectedTo.length }})</button>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
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
                          @vuetable:checkbox-toggled="onCheckboxToggled"
                          @vuetable:checkbox-toggled-all="onCheckboxToggledAll"
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
                                            @click="viewItemButtonClicked(props.rowData.slug, '{{ route('shop.inventory.show', [webUser()->username, 'K']) }}', $event)"
                                            type="button"
                                            class="dropdown-item">View</button>
                                        <button
                                            @click="editItemButtonClicked(props.rowData.slug, '{{ route('shop.inventory.edit', [webUser()->username, 'K']) }}', true, $event)"
                                            type="button"
                                            class="dropdown-item">Edit</button>
                                        <button
                                            type="button"
                                            @click="claimButtonClicked(props.rowData.slug, '{{ route('shop.inventory.show', [webUser()->username, 'K']) }}', $event)"
                                            class="dropdown-item">Claim</button>
                                        <div class="divider"></div>
                                        <button
                                        @click="archiveItem(props.rowData.id, '{{ apiRoute('listables.archive', ['K']) }}', $event)" type="button" class="dropdown-item">Archive</button>
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

    <popout-overlay></popout-overlay>



@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listables-detailed.js') }}"></script>
@endpush