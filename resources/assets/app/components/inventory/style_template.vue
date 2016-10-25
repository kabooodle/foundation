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
                                        data-selected="false"
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
                        v-if="opened_drawers.indexOf(size) > -1"
                        @data-id="{{ size.id }}">
                    <div class="box-divider"></div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="javascript:;"
                                   class=" _500 drawer-toggle"
                                   @click="( opened_drawers.indexOf(size) != 1 ? closeSizeDrawer(size, $event) : openSizeDrawer(size, $event) )"
                                >
                                    {{ size.name }} <span class="text-muted text-sm"><i class="fa fa-angle-up"></i></span>
                                </a>
                            </div>
                            <div class="col-sm-10">
                                <div class="item-box"  v-if="opened_drawers.indexOf(size) > -1" >
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
            var scope = this;

            $Bus.$on('facebook-selection:changed', function(){
                scope.opened_drawers = [];
                $('.btn-group-prpl').find('button.active').removeClass('active');
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
            addToOpenedDrawers:function(obj){
                if (this.opened_drawers.indexOf(obj) == -1) {
                    this.opened_drawers.push(obj);
                }
            },
            removeFromOpenedDrawers:function(obj){
                var index = this.opened_drawers.indexOf(obj);
                if (index != -1) {
                    this.opened_drawers.splice(index, 1);
                }
            },
            openAllSizeDrawers : function(style, event){
                event.preventDefault();
                var scope = this;
                var $el = $(event.currentTarget);
                if($el.hasClass('opened-drawers')) {
                    $el.removeClass('opened-drawers').find('.fa').removeClass('fa-angle-up').addClass('fa-angle-down');
                    $.each(style.sizes, function(){
                        scope.closeSizeDrawer(this, event);
                    });
                } else {
                    $el.addClass('opened-drawers').find('.fa').addClass('fa-angle-up').removeClass('fa-angle-down');
                    $.each(style.sizes, function(){
                        scope.openSizeDrawer(this, event);
                    });
                }
            },
            openSizeDrawer : function(size, event){
                event.preventDefault();
                $('[data-id='+size.id+']').stop(true,true).slideDown();
                this.addToOpenedDrawers(size);
                this.$emit('drawer:opened', size);
            },
            closeSizeDrawer : function(size, event) {
                event.preventDefault();
                $('[data-id='+size.id+']').stop(true,true).slideUp();
                this.removeFromOpenedDrawers(size);
                this.$emit('drawer:closed', size);
            },
            toggleSize : function(style, size, items, event) {
                event.preventDefault();
                var scope = this;
                var $el = $(event.currentTarget);

                if ($el.hasClass('active')) {
                    $.each(items, function(i,v){
                        scope.removeSizeFromSelected(this);
                    });
                    this.closeSizeDrawer(size,event);
                } else {
                    $.each(items, function(i,v){
                        scope.addSizeToSelected(this);
                    });
                    this.openSizeDrawer(size, event);
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
