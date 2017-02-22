/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import DetailedTotals from './DetailedTotals.vue';
import Spinny from '../Spinner.vue';
import Vuetable from '../vuetable/Vuetable.vue';
import VuetablePagination from '../vuetable/VuetablePagination.vue';
import VuetablePaginationInfo from '../vuetable/VuetablePaginationInfo.vue';

new Vue({
    el: '#manage_inventory',
    data: {
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
            {
                name: 'name_alt',
                title: 'Item',
            },
            {
                name: 'qty_on_hand',
                title: 'Qty on hand',
            },
            {
                name: 'accepted_sales_count',
                title: 'Accepted sales',
            },
            {
                name: 'pending_sales_count',
                title: 'Pending claims',
            },
            {
                name: 'pageviews_count',
                title: 'Item views'
            },
            {
                name: 'accepted_price_sum',
                title: 'gross',
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
        // transform: function(data) {
        //     var transformed = {}
        //     var pagination = data.meta.pagination;
        //
        //     let from = ((pagination.current_page * pagination.per_page) - pagination.per_page) + 1;
        //     let to = (pagination.current_page * pagination.per_page);
        //
        //     transformed.pagination = {
        //         per_page: pagination.per_page,
        //         current_page: pagination.current_page,
        //         last_page: pagination.total_pages,
        //         next_page_url: pagination.links.hasOwnProperty('next') ? pagination.links.next : null,
        //         prev_page_url: pagination.links.hasOwnProperty('previous') ? pagination.links.previous : null,
        //         from: from,
        //         to: (to > pagination.total ? pagination.total : to),
        //         total: pagination.total,
        //     }
        //
        //     transformed.data = data.data;
        //
        //     return transformed;
        // },

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
            let body = response.body.totals;
            this.totals.accepted_sales = body.accepted_sales_count;
            this.totals.gross = body.gross;
            this.totals.pageviews = body.pageviews_count;
            this.totals.pending_sales = body.pending_sales_count;
            this.totals.qty_on_hand = body.qty_on_hand;
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
        'detailed-totals' : DetailedTotals,
        'spinny' : Spinny,
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
    }
});