@extends('layouts.full', ['contentId' => 'manage_archives'])

@section('body-content')

    <div class="box white">
        <div class="box-header">
            <div class=" center-block text-center " >
                <div class="row">
                    <div class="col-sm-3">
                        <button
                        @click="bulkActivate('{{ apiRoute('inventory.activate.bulk') }}')"
                        v-if="selectedTo.length"
                        type="button"
                        class="pull-left btn white">Bulk Activate (@{{ selectedTo.length }})</button>
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
                          api-url="{{ apiRoute('inventory.archive.index') }}"
                          pagination-path="pagination"
                          data-path="data"
                          :fields="columns"
                          :per-page="50"
                          :item-actions="itemActions"
                          :append-params="moreParams"
                          @vuetable:loaded="onLoaded"
                          @vuetable:loading="onLoading"
                          @vuetable:load-success="onLoadSuccess"
                          @vuetable:pagination-data="onPaginationData"
                          @vuetable:checkbox-toggled="onCheckboxToggled"
                          @vuetable:checkbox-toggled-all="onCheckboxToggledAll"
                >
                    <template slot="actions" scope="props">
                        <div class="clearfix">
                            <button
                                             class="pull-md-right btn btn-xs white"
                            @click="unarchiveItem(props.rowData.id, props.rowData.unarchive_endpoint, $event)">
                            Unarchive</button>
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
<script src="{{ staticAsset('/assets/js/listables-archive.js') }}"></script>
@endpush