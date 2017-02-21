@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-content')

    <div class="box white">
        <div class="box-body">
            <vuetable ref="vuetable"
                      api-url="{{ apiRoute('inventory.detailed.index', [webUser()->username]) }}"
                      pagination-path=""
                      :fields="columns"
                      :per-page="50"
                      @vuetable:pagination-data="onPaginationData"
            ></vuetable>
        </div>
    </div>


<div class="vuetable-pagination">
    <vuetable-pagination-info ref="paginationInfo"
                              info-class="pagination-info"
    ></vuetable-pagination-info>
    <vuetable-pagination ref="pagination"
                         :css="css.pagination"
                         :icons="css.icons"
                         @vuetable-pagination:change-page="onChangePage"
    ></vuetable-pagination>
</div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/inventory-detailed.js') }}"></script>
@endpush