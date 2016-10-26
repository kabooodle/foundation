<template>
    <div>
        <div v-if="!actions.refreshing_data">
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
                            <div class="pull-left btn-group-prpl" data-toggle="buttons">
                                <button
                                        v-for="size in style.sizes"
                                        @click="toggleSize(style, size, size.items, $event)"
                                        class="btn white btn-xs"
                                        style="margin-right: 3px;">
                                    <input  type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;"> <span class="text-md">{{ size.name }}</span>
                                    <small class="text-sm text-muted block" style="margin-top: -2px;">({{ size.items.length }})</small>
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
                                        <div v-for="item in size.items" class="col-sm-2 btn-group-prpl">
                                            <button
                                                    v-bind:aria-pressed="(selected.items.indexOf(item) != -1 ? 'true' : null )"
                                                    @click="( selected.items.indexOf(item) != -1 ? removeSizeFromSelected(item, $event) : addSizeToSelected(item, $event) )"
                                                    v-bind:class="[ selected.items.indexOf(item) != -1 ? 'active' : '' ] "
                                                    style="border-radius: .25rem;"
                                                    type="button"
                                                    class="btn white btn-xs">
                                                        <span class="item block">
                                                            <img v-bind:src="(item.files ? item.files[0].location : 'http://lorempizza.com/64/64/'+item.id)" class="img-responsive" style="width: 64px; height: 64px;">
                                                        </span>
                                                <span class="p-a-o text-sm clearfix block">
                                                            Qty: <span class="text-muted">{{ item.initial_qty }}</span>  <span class="text-muted">${{ item.price_usd }}</span>
                                                        </span>
                                            </button>
                                            <button
                                                    @click="editItemButtonClicked(item, $event)"
                                                    type="button"
                                                    class="btn btn-xs white"
                                            >Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props: ["inventory_items", "actions", "selected"],
        data: function() {
            return {
                opened_drawers : [],
                active_http_requests : []
            }
        },
        created : function() {
            const scope = this;

            $Bus.$on('facebook-group:changed', function(){
                scope.clearSelectedItems();
            });

            $Bus.$on('facebook-album:changed', function(){
                scope.clearSelectedItems();
                // We want to open the drawers where items are selected.
                let selected_items = scope.selected.items;
                if(selected_items && selected_items.length > 0){
                    _.each(selected_items, function(item){
                        scope.openSizeDrawer(item.style_id, item.size_id);
                    });
                }
            });

            $Bus.$on('inventory:request-reset', function(){

            });

            $Bus.$on('popout-overlay:closed', function(){
                if (scope.active_http_requests.length > 0) {
                    $.each(scope.active_http_requests, function(){
                        this.abort();
                    });
                }
            });
        },
        methods : {
            clearSelectedItems : function(){
                this.opened_drawers = [];
                $('.btn-group-prpl').find('button.active').removeClass('active');
            },
            editItemButtonClicked : function(item, event) {
                $Bus.$emit('popout-overlay:request-open');
                var scope = this;

                this.$http.get('98yhiuj', {
                    before(request) {

                        $Bus.$emit('popout-overlay:change-prompt', false);

                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                        scope.active_http_requests.push(request);
                    }
                }).then(function(response){
                    $Bus.$emit('popout-overlay:change-content', response.body);
                }, function(response){
                    $Bus.$emit('popout-overlay:change-content', 'An error occurred, please try again.', false);
                }).finally(function(){
                    scope.active_http_requests = [];
                });
            },
            addToOpenedDrawers:function(styleid, sizeid){
                var combo = styleid+'_'+sizeid;
                if(! _.contains(this.opened_drawers, combo)){
                    this.opened_drawers.push(combo);
                }
            },
            removeFromOpenedDrawers:function(styleid, sizeid){
                this.opened_drawers = _.without(this.opened_drawers, styleid+'_'+sizeid);
            },
            openAllSizeDrawers : function(style, event){
                event.preventDefault();
                var scope = this;
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
            openSizeDrawer : function(styleid, sizeid, event){
                if (event)  event.preventDefault();
                $('[data-id='+sizeid+']').stop(true,true).slideDown();
                this.addToOpenedDrawers(styleid, sizeid);
                this.$emit('drawer:opened', styleid, sizeid);
            },
            closeSizeDrawer : function(styleid, sizeid, event) {
                event.preventDefault();
                $('[data-id='+sizeid+']').stop(true,true).slideUp();
                this.removeFromOpenedDrawers(styleid, sizeid);
                this.$emit('drawer:closed', styleid, sizeid);
            },
            toggleSize : function(style, size, items, event) {
                event.preventDefault();
                var scope = this;
                var $el = $(event.currentTarget);

                if ($el.hasClass('active')) {
                    $.each(items, function(i,v){
                        scope.removeSizeFromSelected(this);
                    });
                    this.closeSizeDrawer(style.id, size.id,event);
                } else {
                    $.each(items, function(i,v){
                        scope.addSizeToSelected(this);
                    });
                    this.openSizeDrawer(style.id, size.id, event);
                }
            },
            removeSizeFromSelected : function(size) {
                var index = this.selected.items.indexOf(size);
                if (index != -1) {
                    this.selected.items.splice(index, 1);
                }
            },
            addSizeToSelected: function(size) {
                if (this.selected.items.indexOf(size) == -1) {
                    this.selected.items.push(size);
                }
            }
        },
        events : {
            'drawer:opened' : function(size){

            },
            'refreshing_data' : function(){
                this.opened_drawers = [];
            }
        }
    }
</script>
