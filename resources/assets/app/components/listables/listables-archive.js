/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Spinny from '../Spinner.vue';
import Vuetable from '../vuetable/Vuetable.vue';
import VuetablePagination from '../vuetable/VuetablePagination.vue';
import VuetablePaginationInfo from '../vuetable/VuetablePaginationInfo.vue';

new Vue({
    el: '#manage_archives',
    data: {
        selectedTo: [],
        search_filter: null,
        moreParams: {},
        actions: {
            loading: true,
            loaded: false
        },
        totals: {
            gross: 0,
            pageviews: 0,
            qty_on_hand: 0,
            accepted_sales: 0,
            pending_sales: 0
        },
        columns: [
            '__checkbox:id',
            {
                name: 'name_with_cover_photo',
                title: 'Item',
                callback: 'nameWithImg'
            },
            {
                name: 'qty_on_hand',
                title: 'Qty on hand',
            },
            {
                name: 'archived_at',
                title: 'Archived on',
            },
            '__slot:actions'
        ],
        itemActions: [
            {
                name: 'unarchiveItem',
                label: 'Unarchive',
                icon: '',
                class: 'btn white btn-x'
            },
        ],
        css: {
            pagination: {
                wrapperClass: 'pagination',
                activeClass: 'active',
                disabledClass: 'disabled',
                pageClass: 'btn btn-xs white m-a-0',
                linkClass: 'btn btn-xs white m-a-0',
            },
            icons: {
                first: 'fa fa-angle-double-left',
                prev: 'fa fa-angle-left',
                next: 'fa fa-angle-right',
                last: 'fa fa-angle-double-right',
            },
        },
    },

    methods: {
        unarchiveItem(id, endpoint, e){
            this.$emit('deleting-'+id);
            $(e.target).prop('disabled', true).addClass('disabled');
            this.$http.delete(endpoint).then((response)=>{
                $(e.target).closest('tr').fadeOut();
            }, (response)=>{

            });
        },
        transform: function(data) {
            var transformed = {}
            var pagination = data.meta.pagination;

            let from = ((pagination.current_page * pagination.per_page) - pagination.per_page) + 1;
            let to = (pagination.current_page * pagination.per_page);

            transformed.pagination = {
                per_page: pagination.per_page,
                current_page: pagination.current_page,
                last_page: pagination.total_pages,
                next_page_url: pagination.links.hasOwnProperty('next') ? pagination.links.next : null,
                prev_page_url: pagination.links.hasOwnProperty('previous') ? pagination.links.previous : null,
                from: from,
                to: (to > pagination.total ? pagination.total : to),
                total: pagination.total,
            }

            transformed.data = data.data;

            return transformed;
        },
        nameWithImg(val){
            let fields = val.split('::');

            return '<div class="avatar-thumbnail-container"> ' +
                '<div class="avatar-thumbnail _32 m-r-xs"> ' +
                '<img src="'+fields[1]+'"> ' +
                '</div> ' +
                '<span>'+fields[0]+'</span> ' +
                '</div>';
        },
        performSearch(){
            this.moreParams = {
                filter: this.search_filter
            }
            Vue.nextTick( () => this.$refs.vuetable.refresh() )
        },
        onLoaded(){
            this.actions.loading = false;
            this.actions.loaded = true;
        },
        onLoading(){
            this.actions.loading = true;
        },
        onLoadSuccess(response){
            $(function(){
                setTimeout(function(){
                    $("table:not(.no-tablesaw)").each(function(){
                        var currentTable = $(this);
                        currentTable.addClass('tablesaw tablesaw-stack');
                        currentTable.attr("data-tablesaw-mode", "stack");
                        $(document.body).trigger( "enhance.tablesaw" );
                    });
                }, 400);
            });
        },
        onPaginationData (paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
            this.$refs.paginationInfo.setPaginationData(paginationData)
        },
        onChangePage (page) {
            this.$refs.vuetable.changePage(page)
        },
        onCellClicked (data, field, event) {
            console.log('cellClicked: ', field.name)
            this.$refs.vuetable.toggleDetailRow(data.id)
        },
        onCheckboxToggled(checked, item){
            this.selectedTo = this.$refs.vuetable.selectedTo;
        },
        onCheckboxToggledAll(checked){
            this.selectedTo = this.$refs.vuetable.selectedTo;
        },
        bulkActivate(route){
            if (confirmModal(($noty)=>{
                    $noty.$buttons.find('.btn').addClass('disabled').prop('disabled', true);
                    if (this.selectedTo.length && this.selectedTo.length > 0) {
                        this.$http.post(route, {ids: this.selectedTo}).then((response)=>{
                            notify({
                                type: 'success',
                                text: response.body.data.msg
                            });
                            this.selectedTo = [];
                            this.$refs.vuetable.selectedTo = [];
                            this.$refs.vuetable.refresh();
                            setTimeout(function(){
                                $noty.close();
                            },0);
                        }, (response)=>{

                        }).finally(()=>{
                            $noty.$buttons.find('.btn').removeClass('disabled').prop('disabled', false);
                        });
                    }
                },()=>{

                }));
        }
    },
    events: {
        'filter-set' (filterText) {
            this.moreParams = {
                filter: filterText
            }
            Vue.nextTick( () => this.$refs.vuetable.refresh() )
        },
        'filter-reset' () {
            this.moreParams = {}
            Vue.nextTick( () => this.$refs.vuetable.refresh() )
        }
    },
    components: {
        'spinny' : Spinny,
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
    }
});