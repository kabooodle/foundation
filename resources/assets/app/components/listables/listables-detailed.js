/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import PopoutOverlay from '../Popover.vue';
import DetailedTotals from './DetailedTotals.vue';
import Spinny from '../Spinner.vue';
import Vuetable from '../vuetable/Vuetable.vue';
import VuetablePagination from '../vuetable/VuetablePagination.vue';
import VuetablePaginationInfo from '../vuetable/VuetablePaginationInfo.vue';

new Vue({
    el: '#manage_listables',
    data: {
        previousRequest: null,
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
                title: 'Gross',
            },
            '__slot:actions'
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
        archiveItem(id, endpoint, e){
            endpoint = endpoint.replace('K', id);
            this.$emit('archiving-item', id);
            let parentDropdown = $(e.target).closest('.dropdown').find('.dropdown-toggle');
            parentDropdown.prop('disabled', true).addClass('disabled');
            this.$http.put(endpoint).then((response)=>{
                $(e.target).closest('tr').fadeOut();
                notify({
                    type: 'success',
                    text: response.body.data.msg
                });
            }, (response)=>{
                notify({
                    text: response.body.data.msg
                });
                parentDropdown.prop('disabled', false).removeClass('disabled');
            });
        },
        viewItemButtonClicked(slug, route, event){
            window.location.href = route.replace('K', slug);
        },
        editItemButtonClicked(slug, route, ajax, event){
            route = route.replace('K', slug);
            if (!ajax) {
                window.location.href = route;
            } else {
                $Bus.$emit('popout-overlay:request-open');

                this.$http.get(route, {
                    async: false,
                    before(request) {
                        // Before each ajax request, abort the previous request
                        // and add this request to an array of requests for reference.
                        $Bus.$emit('popout-overlay:change-prompt', false);
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response)=>{
                    setTimeout(()=>{
                    $Bus.$emit('popout-overlay:change-content', response.body);
                },0);
            }, (response)=>{
                    $Bus.$emit('popout-overlay:change-content', 'An error occurred, please try again.', false);
                });
            }
        },
        claimButtonClicked(slug, route, event){
            window.location.href = route.replace('K', slug);
        },
        nameWithImg(val){
            let fields = val.split('::');

            return '<div class="avatar-thumbnail-container"> ' +
                '<div class="avatar-thumbnail _32 m-r-xs"> ' +
                '<img data-toggle="lightbox" data-remote="'+fields[1]+'" data-gallery="gallery" src="'+fields[1]+'"> ' +
                '</div> ' +
                '<span>'+fields[0]+'</span> ' +
                '</div>';Handl
        },
        performSearch(){
            this.moreParams = {
                filter: this.search_filter
            }
            Vue.nextTick( () => this.$refs.vuetable.refresh() )
        },
        bulkDelete(route){
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
        onCheckboxToggled(checked, item){
            this.selectedTo = this.$refs.vuetable.selectedTo;
        },
        onCheckboxToggledAll(checked){
            this.selectedTo = this.$refs.vuetable.selectedTo;
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
        'detailed-totals' : DetailedTotals,
        'popout-overlay' : PopoutOverlay,
        'spinny' : Spinny,
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
    }
});