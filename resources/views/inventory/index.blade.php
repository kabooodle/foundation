@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    <div class="center-block text-center">
        <div class="btn-group dropdown ">
            <button :disabled="selected_items.length == 0 || ! ready"
                v-bind:class="{'disabled': selected_items.length == 0 || ! ready}"
                class="disabled btn white btn-sm dropdown-toggle"
                data-toggle="dropdown">
                <span class="dropdown-label">Bulk (@{{selected_items.length}})</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item text-danger" href="">Delete</a>
            </div>
        </div>
        <button class="btn white btn-sm " id="navbarSideButton">Filter Items</button>
        <button class="btn white btn-sm " data-toggle="tooltip" v-on:click="getInventory" :disabled="refreshing_data" title="Refresh Inventory" data-placement="top" ><i class="fa fa-refresh"></i></button>
    </div>
@endsection


@section('body-content')

    <style>
        .b-prpl {
            border-color: #9446ed;
            border: 1px solid #9446ed;
        }
        #selected_items_total {
            padding: 12px;
            font-size: 20px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 2000;
            border: 1px solid #9446ed;
            color: #9446ed;
            background-color: #fff;
            border-radius: .25rem;
        }
    </style>

    <style-template :data_items.sync="data_items" :refreshing_data.sync="refreshing_data"></style-template>

    <script type="text/template" id="style-template">
        <div>
            <div v-if="data_items.length == 0 || refreshing_data" class="box m-b-0" style="min-height: 400px; position: relative;">
                <div class="text-center center-block" style="position: absolute; left: 50%; top: 50%; margin-left: -5px; margin-top: -5px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>
            <div v-if="!refreshing_data">
                <div class="box style-container" v-for="style in data_items">
                    <div class="box-header clearfix">
                        <div class="row">
                            <div class="col-sm-2" style="margin-top: 13px;">
                                <h6 class="m-a-0">
                                    <a href="javascript:;" v-on:click="openAllSizeDrawers(style, $event)">@{{ style.name }} <span class="text-muted text-sm"><i class="fa fa-angle-down"></i></span></a>
                                    <small class="text-sm text-muted">(@{{ style.total }})</small>
                                </h6>
                            </div>
                            <div class="col-sm-10" style="margin-top: 3px;">
                                <div class="pull-left btn-group-prpl" data-toggle="buttons">
                                    <span v-for="size in style.sizes">
                                        <label data-selected="false" v-on:click="toggleSize(style, size, size.items, $event)" class="btn white btn-xs" style="">
                                            <input  type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;"> <span class="text-md">@{{ size.name }}</span>
                                            <small class="text-sm text-muted block" style="margin-top: -2px;">(@{{ size.items.length }})</small>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-for="size in style.sizes" class="box-size-drawer" style="display:none;" data-id="@{{ size.id }}">
                        <div class="box-divider"></div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <a href="javascript:;" class=" _500 drawer-toggle" v-on:click="( opened_drawers.indexOf(size) != 1 ? closeSizeDrawer(size, $event) : openSizeDrawer(size, $event) )">
                                        @{{ size.name }} <span class="text-muted text-sm"><i class="fa fa-angle-up"></i></span></a>
                                </div>
                                <div class="col-sm-10">
                                    <div class="item-box"  v-if="opened_drawers.indexOf(size) > -1" >
                                        <div class="row row-horizon">
                                            <div v-for="item in items" class="col-sm-2">
                                                <div class="box m-b-0 p-a-xs" v-on:click="remove" v-bind:class="{'b-prpl' : selected_items.indexOf(item) > -1}" style="border-radius: .25rem;">
                                                    <div class="item">
                                                        <img v-bind:src="(item.files ? item.files[0].location : 'https://placekitten.com/g/32/20')" class="img-responsive" style="width: 64px; height: 64px;">
                                                    </div>
                                                    <div class="p-a-o text-sm clearfix">
                                                        Qty: <span class="text-muted">@{{ item.initial_qty }}</span>  <span class="text-muted">$@{{ item.price_usd }}</span>
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
            </div>

        </div>
    </script>

    <script type="text/template" id="size-item-template">

    </script>

    <script>
        Vue.component('size-item', {
            props: ["items", "parsed_items", "selected_items"],
            template: '#size-item-template'
        });

        Vue.component('style-template', {
            template: '#style-template',
            props: ["data_items", "refreshing_data"],
            data: function() {
                return {
                    selected_items : [],
                    opened_drawers : []
                }
            },
            methods : {
                addToOpenedDrawers:function(obj){
                    if (this.opened_drawers.indexOf(obj) < 1) {
                        this.opened_drawers.push(obj);
                    }
                },
                removeFromOpenedDrawers:function(obj){
                    this.opened_drawers.$remove(obj);
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
                    this.$dispatch('drawer:opened', size);
                },
                closeSizeDrawer : function(size, event) {
                    event.preventDefault();
                    $('[data-id='+size.id+']').stop(true,true).slideUp();
                    this.removeFromOpenedDrawers(size);
                    this.$dispatch('drawer:closed', size);
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
                    this.selected_items.$remove(size);
                },
                addSizeToSelected: function(size) {
                    if ($.inArray(size, this.selected_items) != 1) {
                        this.selected_items.push(size);
                    }
                }
            },
            events : {
                'drawer:opened' : function(size){

                },
                'refreshing_data' : function(){
                    this.selected_items = [];
                    this.opened_drawers = [];
                }
            }
        });

        new Vue({
            el: '#manage_inventory',
            data : {
                data_items : [],
                refreshing_data: false
            },
            ready : function() {
                this.getInventory();
            },
            methods: {
                remove : function() {
                    alert('hi');
                },
                getInventory : function() {
                    var scope = this;
                    scope.refreshing_data = true;
                    scope.$broadcast('refreshing_data');
                    scope.data_items = [];
                    $.ajax({
                        method: 'GET',
                        url : window.location.href
                    }).success(function(response){
                        scope.data_items = response;
                    }).always(function(){
                        scope.refreshing_data = false;
                    });
                }
            }
        });
    </script>
@endsection