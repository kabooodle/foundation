/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import Vuetable from '../vuetable/Vuetable.vue';
import VuetablePagination from '../vuetable/VuetablePagination.vue';
import VuetablePaginationInfo from '../vuetable/VuetablePaginationInfo.vue';

new Vue({
    el: '#manage_inventory',
    data: {
        columns: [
            {
                name: 'name_alt',
                title: 'Item',
            },
            'qty_on_hand',
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
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
    }
});