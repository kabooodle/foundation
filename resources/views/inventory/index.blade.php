@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')
    {{--<div class="center-block text-center">--}}
    {{--<div class="btn-group dropdown ">--}}
    {{--<button :disabled="selectedItems.length == 0 || ! ready"--}}
    {{--v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"--}}
    {{--class="disabled btn white btn-sm dropdown-toggle"--}}
    {{--data-toggle="dropdown">--}}
    {{--<span class="dropdown-label">Bulk (@{{selectedItems.length}})</span>--}}
    {{--<span class="caret"></span>--}}
    {{--</button>--}}
    {{--<div class="dropdown-menu text-left text-sm">--}}
    {{--<a class="dropdown-item text-danger" href="">Delete</a>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<button class="btn white btn-sm " id="navbarSideButton">Filter Items</button>--}}
    {{--</div>--}}
@endsection


@section('body-content')

    <style>
        #selected_items_total {
            padding: 12px;
            font-size: 20px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            border: 1px solid #9446ed;
            color: #9446ed;
            background-color: #fff;
            border-radius: .25rem;
        }
    </style>

    <style-template :data_items="{{ $groupings  }}"></style-template>

    <script type="text/template" id="style-template">
        <div>
            <div class="box" v-for="style in data_items">
                <div class="box-header clearfix">
                    <div class="row">
                        <div class="col-sm-3" style="margin-top: 13px;">
                            <h6 class="m-a-0">
                                <a v-on:click="openAllSizeDrawers(style, $event)">@{{ style.name }} <span class="text-muted text-sm"><i class="fa fa-angle-down"></i></span></a>
                                <small class="text-sm text-muted">(@{{ style.total }})</small>
                            </h6>
                        </div>
                        <div class="col-sm-9" style="margin-top: 3px;">
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
                <div  v-for="size in style.sizes">
                    <div class="box-size-drawer" style="display:none;" data-id="@{{ size.id }}">
                        <div class="box-divider"></div>
                        <div class="box-body">
                            <div class="m-l-1">
                                <a class=" _500 drawer-toggle" v-on:click="( opened_drawers.indexOf(size) != 1 ? closeSizeDrawer(size, $event) : openSizeDrawer(size, $event) )">@{{ size.name }} <span class="text-muted text-sm"><i class="fa fa-angle-up"></i></span></a>
                                <div class="row size-thumb">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="selected_items_total" class="box back-to-top animated tada" v-show="selected_items.length > 0">
                <span class="_600">@{{ selected_items.length }}</span> Items Selected
            </div>
        </div>
    </script>

    <script type="text/template" id="size-item-template">
        <div>
            <div v-for="item in items">
                @{{ item.price_usd }}
            </div>
        </div>
    </script>

    <script>
        Vue.component('size-item', {
            template: '#size-item-template'
        });

        Vue.component('style-template', {
            template: '#style-template',
            props: {
                data_items: {
                    type: Array,
                    required: true
                }
            },
            data: function() {
                return {
                    selected_items : [],
                    opened_drawers : []
                }
            },
            methods : {
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
                    this.opened_drawers.push(size);
                    $('[data-id='+size.id+']').slideDown();
                },
                closeSizeDrawer : function(size, event) {
                    event.preventDefault();
                    this.opened_drawers.$remove(size);
                    $('[data-id='+size.id+']').slideUp();
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
            }
        });


        new Vue({
            el: '#manage_inventory'
        });
    </script>
@endsection