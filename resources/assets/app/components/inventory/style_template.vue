<template>
    <div v-if="actions.refreshing_data" class="text-block text-center" style="min-height: 300px">
        <div style="margin:200px auto 0;">
            <spinny size="40"></spinny>
        </div>
    </div>
    <div v-else>
            <div class="box style-container" v-for="style in inventory_items">
                <div class="box-header clearfix">
                    <div class="row">
                        <div class="col-sm-2" style="margin-top: 13px;">
                            <h6 class="m-a-0">
                                <a href="javascript:;" @click="openAllSizeDrawers(style, $event)">{{ style.name }} <span class="text-muted text-sm"><i class="fa fa-angle-down"></i></span></a>
                                <small class="text-sm text-muted">({{ style.total }})</small>
                            </h6>
                        </div>
                        <div class="col-sm-10" style="margin-top: 3px;">
                            <div class="pull-left btn-group-prpl">
                                <template v-for="size in style.sizes">
                                <button
                                        class="btn white btn-xs"
                                        v-bind:aria-pressed="(selectedItems.find(s => s.style_id === style.id && s.size_id === size.id ) ? ' true ' : null )"
                                        v-bind:class="[ selectedItems.find(s => s.style_id === style.id && s.size_id === size.id )  ? 'sex active' : '' ] "
                                        @click="( selectedItems.find(s => s.style_id === style.id && s.size_id === size.id ) ? removeSizes(style, size, size.items, $event) : addSizes(style, size, size.items, $event) )"
                                        style="margin-right: 3px;">
                                    <input  type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;"> <span class="text-md">{{ size.name }}</span>
                                    <small class="text-sm text-muted block" style="margin-top: -2px;">({{ size.items.length }})</small>
                                </button>
                                </template>
                                <button
                                        @click="selectAllOfStyle(style, $event)"
                                        class="btn white btn-xs"
                                        style="margin-left: 12px;">
                                    <input  type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;"> <span class="text-md">ALL</span>
                                    <small class="text-sm text-muted block" style="margin-top: -2px;">({{ style.sizes.length }})</small>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                        v-for="size in style.sizes"
                        class="box-size-drawer"
                        v-if="opened_drawers.indexOf(style.id+'_'+size.id) > -1"
                        @data-id="{{ size.id }}">
                    <div class="box-divider"></div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="javascript:;"
                                   class=" _500 drawer-toggle"
                                   @click="(opened_drawers.indexOf(style.id+'_'+size.id) > -1 ? closeSizeDrawer(style.id, size.id, $event) : openSizeDrawer(style.id, size.id, $event) )"
                                >
                                    {{ size.name }} <span class="text-muted text-sm"><i class="fa fa-angle-up"></i></span>
                                </a>
                            </div>
                            <div class="col-sm-10">
                                <div class="item-box"  v-if="opened_drawers.indexOf(style.id+'_'+size.id) > -1" >
                                    <div class="row row-horizon">
                                        <template v-for="item in size.items">
                                            <div class="col-sm-2 btn-group-prpl" style="width:122px !important;">
                                                <button
                                                        v-bind:aria-pressed="(selectedItems.indexOf(item) > -1 ? ' true ' : null )"
                                                        @click="( selectedItems.indexOf(item) > -1 ? removeSizeFromSelected(item, $event) : addSizeToSelected(item, $event) )"
                                                        v-bind:class="[ selectedItems.indexOf(item) > -1 ? 'active' : '' ] "
                                                        style="border-radius: .25rem;"
                                                        type="button"
                                                        class="btn white btn-xs">
                                                            <span class="item block">
                                                                <img v-bind:src="(item.files && item.files.length > 0 ? item.files[0].location : 'http://lorempizza.com/64/64/'+item.id)" class="img-responsive" style="width: 80px; height: 80px;">
                                                            </span>
                                                    <span class="p-a-o text-sm clearfix block">
                                                        <span class="pull-left">Qty: <span class="text-muted">{{ item.initial_qty }}</span></span>
                                                            <span class="text-muted pull-right">${{ item.price_usd }}</span>
                                                            </span>
                                                </button>
                                                <div class="clearfix" style="margin-top: 5px;">
                                                <button
                                                        @click="editItemButtonClicked(item, $event)"
                                                        type="button"
                                                        class="btn btn-xs pull-left white"
                                                >Edit</button>
                                                <a
                                                        target="_blank"
                                                        v-bind:href="this.window.location.href+'/'+item.name_uuid"
                                                        class="btn btn-xs pull-right white"
                                                >View</a>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>
<style>
    div.col-sm-2.btn-group-prpl {
        width:122px !important;
    }
</style>
<script>
    import computed from './manage/computed';
    import Spinny from '../Spinner.vue';

    export default{
        data(){
            return {
                opened_drawers : [],
                active_http_requests : []
            }
        },
        computed,
        created(){
            const scope = this;

            $Bus.$on('facebook-group:changed', ()=>{
                this.clearSelectedItems();
                $('.facebook_album_radio').prop('checked', false);
            });

            $Bus.$on('inventory:select-all', ()=>{
                $.each(this.inventory_items, function(){
                    $.each(this.sizes,function(){
                        $.each(this.items, function(){
                            scope.addSizeToSelected(this);
                        });
                    })
                });
            });

            $Bus.$on('facebook-album:changed', ()=>{
                this.clearSelectedItems();
                // We want to open the drawers where items are selected.
                let selected_items = scope.selected.items;
                if(selected_items && selected_items.length > 0){
                    _.each(selected_items, (item)=>{
                        scope.openSizeDrawer(item.style_id, item.size_id);
                    });
                }
            });

            // Each time an inventory is updated using ajax, we want to update the item's state.
            // Once the item state is updated, everywhere referencing the state will update seamlessly.
            $Bus.$on('inventory-item:updated', (item, updatedItem)=>{
                let styleIndex = -1;
                let sizeIndex = -1;
                let itemIndex = -1;

                // Because we want to update state and not a local variable, we need the full
                // "path" to the state so we can set it.  This is about 3 indexes deep so we have to
                // find each ones index individually :o

                // find the style index.
                _.each(this.inventory_items, (style,index)=>{
                    if(style.id == item.style.id) {
                        styleIndex = index;
                    }
                });

                // find the size index.
                _.each(this.inventory_items[styleIndex].sizes, (size, index)=>{
                    if(size.id == item.style_size.id){
                        sizeIndex = index;
                    }
                });

                // find the item index/
                _.each(scope.inventory_items[styleIndex].sizes[sizeIndex].items, (inventoryItem, index)=>{
                    if(inventoryItem.id == item.id) {
                        itemIndex = index;
                    }
                });

                // update the state.
                this.inventory_items[styleIndex].sizes[sizeIndex].items[itemIndex] = updatedItem;
                this.selected.items = [];
                $Bus.$emit('popout-overlay:close');
            });

            $Bus.$on('inventory:request-reset', ()=>{

            });

            $Bus.$on('popout-overlay:closed', ()=>{
                // Cancel all pending ajax requests when we close the popout.
                if (scope.active_http_requests.length > 0) {
                    $.each(scope.active_http_requests, ()=>{
                        this.abort();
                    });
                }
            });
        },
        methods : {
            selectAllOfStyle(style, event){
                const scope = this;
                $.each(style.sizes,function(){
                    $.each(this.items, function(){
                        scope.addSizeToSelected(this);
                    })
                });
            },
            clearSelectedItems(){
                this.opened_drawers = [];
            },
            editItemButtonClicked(item, event){
                $Bus.$emit('popout-overlay:request-open');

                this.$http.get(window.location.href+'/'+item.name_uuid+'/edit', {
                    async: false,
                    before(request) {
                        // Before each ajax request, abort the previous request
                        // and add this request to an array of requests for reference.
                        $Bus.$emit('popout-overlay:change-prompt', false);
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                        this.active_http_requests.push(request);
                    }
                }).then((response)=>{
                    setTimeout(()=>{
                        $Bus.$emit('popout-overlay:change-content', response.body);
                    },0);
                }, (response)=>{
                    $Bus.$emit('popout-overlay:change-content', 'An error occurred, please try again.', false);
                })
                .finally(()=>{
                    this.active_http_requests = [];
                });
            },
            addToOpenedDrawers(styleid, sizeid){
                var combo = styleid+'_'+sizeid;
                if(! _.contains(this.opened_drawers, combo)){
                    this.opened_drawers.push(combo);
                }
            },
            removeFromOpenedDrawers(styleid, sizeid){
                this.opened_drawers = _.without(this.opened_drawers, styleid+'_'+sizeid);
            },
            openAllSizeDrawers(style, event){
                event.preventDefault();
                const scope = this;
                var $el = $(event.currentTarget);
                if($el.hasClass('opened-drawers')) {
                    $el.removeClass('opened-drawers').find('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
                    $.each(style.sizes, function(){
                        scope.closeSizeDrawer(style.id, this.id, event);
                    });
                } else {
                    $el.addClass('opened-drawers').find('.fa').addClass('fa-angle-up').removeClass('fa-angle-down');
                    $.each(style.sizes, function(){
                        scope.openSizeDrawer(style.id, this.id, event);
                    });
                }
            },
            openSizeDrawer(styleid, sizeid, event){
                if (event)  event.preventDefault();
                $('[data-id='+sizeid+']').stop(true,true).slideDown();
                this.addToOpenedDrawers(styleid, sizeid);
                this.$emit('drawer:opened', styleid, sizeid);
            },
            closeSizeDrawer(styleid, sizeid, event){
                event.preventDefault();
                $('[data-id='+sizeid+']').stop(true,true).slideUp();
                this.removeFromOpenedDrawers(styleid, sizeid);
                this.$emit('drawer:closed', styleid, sizeid);
            },
            // Remove size from selected list
            removeSizes(style,size,items,event){
                const scope = this;
                $.each(items, function(i,v){
                    scope.removeSizeFromSelected(this);
                });
                this.closeSizeDrawer(style.id, size.id,event);
            },
            addSizes(style,size,items,event){
                const scope = this;
                $.each(items, function(i,v){
                    scope.addSizeToSelected(this);
                });
                this.openSizeDrawer(style.id, size.id, event);
            },
            removeSizeFromSelected(size){
                var index = this.$store.getters.getSelectedItems.indexOf(size);
                if (index != -1) {
                    this.$store.commit('REMOVE_FROM_SELECTED_ITEMS', index);
                }
            },
            addSizeToSelected(size){
                if (this.$store.getters.getSelectedItems.indexOf(size) == -1) {
                    this.$store.commit('ADD_TO_SELECTED_ITEMS', size);
                }
            }
        },
        events : {
            'drawer:opened' : (size)=>{

            },
            'refreshing_data' : ()=>{
                this.opened_drawers = [];
            }
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
