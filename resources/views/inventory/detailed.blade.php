@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')

    {{--<div class="pull-left">--}}
        {{--<div class="btn-toolbar center-block text-center">--}}


            {{--<a :disabled="selectedItems.length == 0 || ! ready"--}}
               {{--v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"--}}
               {{--data-toggle="modal" data-target="#m-md"--}}
               {{--data-backdrop="static" data-keyboard="false" href="#"--}}
               {{--class="disabled btn_add_selected text-white  btn btn-sm warning">Add Selected--}}
                {{--Items to Sale</a>--}}

            {{--<a :disabled="selectedItems.length == 0 || ! ready"--}}
               {{--v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"--}}
               {{--data-toggle="modal" data-target="#m-md-fb"--}}
               {{--data-backdrop="static" data-keyboard="false" href="#"--}}
               {{--class="disabled text-white btn_add_selected btn btn-sm warning">Add Selected--}}
                {{--Items to Facebook</a>--}}
        {{--</div>--}}
    {{--</div>--}}


    <div class="center-block text-center">
        <div class="btn-group dropdown ">
            <button :disabled="selectedItems.length == 0 || ! ready"
                    v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"
                    class="disabled btn white btn-sm dropdown-toggle"
                    data-toggle="dropdown">
                <span class="dropdown-label">Bulk (@{{selectedItems.length}})</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item text-danger" href="">Delete</a>
            </div>
        </div>
        <button class="btn white btn-sm " id="navbarSideButton">Filter Items</button>
        {{--<a href="{{ route('shop.inventory.create', [user()->username]) }}" class="btn btn-sm white">Add Items</a>--}}
    </div>

@endsection


@section('body-content')

    <script type="text/javascript" src="http://cdn.jsdelivr.net/vue.table/1.5.6/vue-table.min.js"></script>

    <div class="navbar-side p-a" id="navbarSide">
        <div class="box ">
            <div class="box-body clearfix">
                <form>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">Style</label>
                        <div class="col-sm-8">
                            {{ Form::select('style_id[]', $filters['styles']->pluck('name','id')->toArray(), null, ['data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">Size</label>
                        <div class="col-sm-8">
                            {{ Form::select('size_id[]', $filters['sizes']->pluck('name','id')->toArray(), null, ['data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">Flash Sales</label>
                        <div class="col-sm-8">
                            {{ Form::select('flashsale_id[]', $filters['flashSales']->pluck('name','id')->toArray(), null, ['data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">
                            Qty of 0
                        </label>
                        <div class="col-sm-8 ">
                            <input type="checkbox" name="qty_0" class="form-control" style="margin-top: 8px;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">
                            Has Sales
                        </label>
                        <div class="col-sm-8 ">
                            <input type="checkbox" name="has_sales" class="form-control" style="margin-top: 8px;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-4 text-sm">
                            Has Claims
                        </label>
                        <div class="col-sm-8 ">
                            <input type="checkbox" name="has_claims" class="form-control" style="margin-top: 8px;">
                        </div>
                    </div>
                    <div class="form-group row p-b-0 m-b-0">
                        <div class="col-sm-8 col-sm-offset-4">
                            <button type="button" v-on:click="filterSubmit" :disabled="!ready" class="btn-sm btn primary">Go</button>
                            <button type="button" class="btn white btn-sm" v-on:click="hideFilterMenu">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="table-wrapper">
        <div v-show="moreParams.length > 0"><small class="text-muted text-center center-block">(Filtered Results)</small></div>
        <div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i></div>

        <vuetable
                pagination-class="clearfix "
                pagination-info-class="pull-left text-sm"
                pagination-component-class="pull-right text-sm"
                :fields="columns"
                :item-actions="itemActions"
                :append-params="moreParams"
                :selected-to="selectedItems"
                per-page="50"
                row-class-callback="rowClassCB"
                table-wrapper=".table-wrapper"
                pagination-path=""
                api-url="http://app.kabooodle.dev/shop/jaketoolson/inventory"
                table-class=" table table-condensed table-as-list white "
        ></vuetable>
    </div>

    <div id="m-md" class="modal" data-backdrop="true">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select sales to assign item</h5>
                        </div>
                        <div class="modal-body">
                            @foreach(user()->flashsalesAsSellerAndAdmins as $flashSale)
                                <div class="form-group">
                                    <label class="md-check">
                                        <input type="checkbox" class="flashsale_id_el has-value" name="flashsales[]" value="{{ $flashSale->id }}">
                                        <i class="green"></i>
                                        {!! $flashSale->name !!}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn primary" v-on:click="addItemsToSale" >Save</button>
                            <button type="button" class="m-l-1 btn white" data-dismiss="modal">Cancel</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>
    </div>


    <div id="m-md-fb" class="modal" data-backdrop="true" style="display: none;">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{ Form::open(['class' => 'form-save']) }}
                        <div class="modal-header">
                            <h5 class="modal-title">Select sales to assign item</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Facebook Group</label>
                                {{ Form::select('fb_group', user()->present()->getFacebookGroupsForList(), [] , ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group">
                                <label>Select Album(s)</label>
                                {{ Form::select('fb_albums[]', user()->present()->getFacebookAlbumsByFroupForList(327095390958693), [] , ['class' => 'form-control', 'multiple']) }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn primary p-x-md btn_save_selected" id="">Save</button>
                            <button type="button" class="m-l-1 btn white" data-dismiss="modal">Close</button>
                        </div>
                        {{ Form::close() }}
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

    </div>

    <style>
        .noscroll {
            overflow-x: hidden;
            overflow-y: hidden;
        }

        .shot-overlay {
            background: rgba(30,30,30,0.9);
            top: 0;
            left: 0;
            z-index: 9997;
            width: 100%;
            height: 100%;
            position: fixed;
            overflow-y: scroll;
            box-sizing: border-box;
            -webkit-overflow-scrolling: touch;
        }
        a.close-overlay {
            position: absolute;
            top: 0;
            right: 0;
            width: 20px;
            height: 20px;
            padding: 20px;
            opacity: .5;
            z-index: 9998;
            background-repeat: no-repeat;
            background-position: 20px 20px;
            background-image: url(https://dribbble.com/assets/icon-shot-x-light-40c073cd65443c99d4ac129b69bf578c8cf97d69b78990c00c4f8c5873b0d601.png);
        }
        .overlay-content {
            position: absolute;
            z-index: 9997;
            top: 20px;
            left: 50%;
            width: 920px;
            min-height: 400px;
            margin-left: -460px;
            padding: 40px 40px;
            background: #f4f4f4;
            background-position: fixed;
            box-sizing: border-box;
            border-radius: 6px;
        }
        a.close-overlay img {
            height: 0;
        }
        @media only screen and (max-width: 959px) {
            .shot-overlay {
                overflow-y: auto;
            }
            a.close-overlay {
                opacity: .34;
                background-image: url(/assets/icon-shot-x-7dbc9cdd6856806bcc277a21513ac00fd402944f9046212dfe5151e1bb3a5ab8.png);
            }
            .overlay-content {
                left: auto;
                top: 0;
                width: 100%;
                max-width: 100%;
                margin-left: 0;
                padding: 20px;
                border-radius: 0;
            }
        }
    </style>

    <script type="text/template" id="popout-overlay">
        <div class="pop-out-overlay shot-overlay" style="display: none;">
            <a href="javascript:;" class="close-overlay" v-on:click="closeOverlay" aria-label="close">
                <img src="https://d13yacurqjgara.cloudfront.net/assets/icon-shot-x-light-40c073cd65443c99d4ac129b69bf578c8cf97d69b78990c00c4f8c5873b0d601.png" alt="Icon shot x light">
            </a>
            <div class="overlay-content group">

            </div>
        </div>
    </script>

    <popout-overlay></popout-overlay>

    <script>
        Vue.component('popout-overlay', {
            template: '#popout-overlay',
            data: function() {
                return {
                    promptOnClose : true,
                    defaultContent: '<div class="text-center center-block" style="position: absolute; top: 50%; margin-top: -20px; margin-left: -20px; left: 50%; "><i class="fa fa-2x fa-spin fa-spinner"></i></div>'
                }
            },
            ready : function() {
                this.resetOverlay();
            },
            methods : {
                openOverlay : function(content){
                    $('body').addClass('noscroll');
                    $('.shot-overlay').show();
                    if(content) {
                        this.changeOverlayContent(content);
                    }
                    this.$dispatch('popout-overlay:opened');
                    this.$broadcast('popout-overlay:opened');
                },
                resetOverlay : function() {
                    $('body').removeClass('noscroll');
                    $('.shot-overlay').hide();
                    this.changeOverlayContent(this.defaultContent);
                    this.$dispatch('popout-overlay:closed');
                    this.$broadcast('popout-overlay:closed');
                },
                closeOverlay : function() {
                    var scope = this;
                    if (this.promptOnClose) {
                        confirmModal(function(noty){
                            scope.resetOverlay();
                            noty.close();
                        });
                    } else {
                        scope.resetOverlay();
                    }
                },
                changeOverlayContent : function(content) {
                    $('.shot-overlay').find('.overlay-content').html(content);
                }
            },
            events : {
                'popout-overlay:close' : function(){
                    this.closeOverlay();
                },
                'popout-overlay:request-open' : function(content) {
                    this.openOverlay(content);
                },
                'popout-overlay:change-content' : function(content) {
                    this.changeOverlayContent(content);
                }
            }
        });

        Vue.component('dropdown', {
            template: ['' +
            '<div class="dropdown pull-right m-r-1">' +
                '<button class="btn btn-xs white dropdown-toggle" data-toggle="dropdown"></button>' +
                '<div class="dropdown-menu dropdown-menu-right text-sm"> ' +
                    '<a class="dropdown-item" href="javascript:;" v-on:click="previewItem(rowData)">View</a> ' +
                    '<a class="dropdown-item" href="javascript:;" v-on:click="editItem(rowData)">Edit</a> ' +
                    '<a class="dropdown-item" v-show="rowData.pending_claims.length > 0" href="javascript:;" v-on:click="acceptClaim(rowData)">Approve Claims</a> ' +
                    '<a class="dropdown-item" v-show="rowData.pending_claims.length > 0" href="javascript:;" :disabled="rowData.pending_claims.length == 0" v-on:click="acceptClaim(rowData)">Reject Claims</a> ' +
                    '<hr style="margin-top: .35rem; margin-bottom: .35rem;">' +
                    '<a class="dropdown-item text-danger" href="javascript:;" v-on:click="deleteItem(rowData)">Delete</a> ' +
                '</div>' +
            '</div>' +
            ''].join(''),
            props: {
                rowData: {
                    type: Object,
                    required: true
                }
            },
            methods : {
                previewItem : function(item) {
                    $('.table-wrapper').addClass('loading');
                    window.location.href =  window.location.href + '/'+item.uuid;
                },
                editItem: function(item) {
                    $('.table-wrapper').addClass('loading');
                    window.location.href =  window.location.href + '/'+item.uuid+'/edit';
                },
                deleteItem: function(item) {
                    confirmModal(function(noty){
                        noty.close();
                    }, {}, {
                        text: '<h6>Delete the '+ item.name +'?</h6><small class="block">(Size: '+item.style_size.name+', Qty: '+item.initial_qty+')</small>'
                    });
                }
            }
        });

        Vue.component('checkbox', {
            template: ['<input type="checkbox" @click="checkboxClicked(rowData, $event)">'].join(''),
            props: {
                rowData: {
                    type: Object,
                    required: true
                }
            },
            methods: {
                checkboxClicked: function (data, event) {
                    this.$dispatch('checkbox:clicked', event, data);
                }
            }
        });

        new Vue({
            el: '#manage_inventory',
            data: {
                ready : false,
                moreParams: [],
                selectedItems: [],
                columns: [
                    {
                        name: '__component:checkbox',
                        titleClass: 'text-center',
                        dataClass: 'text-center'
                    },
                    {
                        name: 'files',
                        callback: 'setPropItem'
                    },
                    {
                        name: 'style',
                        callback: 'setPropStyle'
                    },
                    {
                        name: 'style_size',
                        title: 'size',
                        callback: 'setPropSize'
                    },
                    {
                        name: 'price_usd',
                        title: 'Price',
                        callback: 'setPropPrice'
                    },
                    {
                        name: 'initial_qty',
                        title: 'Qty'
                    },
                    {
                        name: 'pending_claims',
                        title: 'claims',
                        callback: 'setPropClaims'
                    },
                    {
                        name: 'sales',
                        callback: 'setPropSales'
                    },
                    {
                        name: '__component:dropdown'
                    }
                ]
            },
            methods: {
                paginationConfig: function(componentName) {
                    this.$broadcast('vuetable-pagination:set-options', {
                        wrapperClass: 'pagination',
                        icons: {
                            first: '',
                            prev: '',
                            next: '',
                            last: ''
                        },
                        activeClass: 'active',
                        linkClass: 'btn btn-default btn-sm',
                        pageClass: 'btn btn-default btn-sm'
                    })
                },
                hideFilterMenu : function(){
                    $('#navbarSide').removeClass('reveal');
                },
                resetMoreParams: function(){
                    this.$data.moreParams = [];
                },
                filterSubmit: function (e) {
                    e.preventDefault();
                    var scope = this;
                    var el = $(e.target);
                    var form = el.closest('form');

                    scope.resetMoreParams();
                    scope.moreParams.push(form.serialize());
                    setTimeout(function(){
                        scope.$broadcast('vuetable:refresh');
                    },0);
                },
                setPropSales: function (v) {
                    return v.length;
                },
                setPropClaims: function (v) {
                    return v.length;
                },
                setPropItem: function (v) {
                    var firstImg = v ? v[0] : null;
                    return firstImg ? '<img style="width: 32px; height: 32px; border-radius: 2px;" src="' + firstImg.location + '">' : null;
                },
                setPropSize: function (v) {
                    return v.name;
                },
                setPropStyle: function (v) {
                    return v.name;
                },
                setPropPrice: function (v) {
                    return '$' + v;
                },
                rowClassCB: function (data, index) {
                    return 'p-l-0 m-l-0';
                },
                addItemsToSale: function(){
                    var scope = this;
                    var $el = $(this);
                    var inventory_id = [];
                    var flashsale_id = [];

                    var fsEl = $('.flashsale_id_el:checked');

                    $.each(fsEl, function(i,k){
                        flashsale_id.push($(this).val());
                    });

                    $.each(this.selectedItems, function(i,k){
                        inventory_id.push(this.id);
                    });

                    if(inventory_id.length == 0 || flashsale_id.length == 0) {
                        alert('A flash sale or item must first be selected');
                        return false;
                    }

                    $el.addClass('disabled').prop('disabled', true);

                    $.ajax({
                        data: {
                            inventory_id: inventory_id,
                            flashsale_id: flashsale_id
                        },
                        dataType: "json",
                        url: "{{ apiRoute('inventory.associate.store', [user()->username]) }}",
                        method: "POST",
                        success: function (response) {
                            $el.html('Success!');
                            setTimeout(function(){
                                $el.removeClass('disabled').prop('disabled', false);
                            },500);
                        },
                        error: function () {
                            alert('An error occurred, please try again.');
                        }
                    });
                }
            },
            ready : function(){

            },
            events: {
                'vuetable:cell-dblclicked' : function(data,field,event) {
                    var scope = this;
                    scope.$broadcast('popout-overlay:request-open');
                    $.ajax({
                        url : 'http://app.kabooodle.dev/shop/jaketoolson/inventory/lindsey__jRS/edit'
                    }).success(function(response){
                        scope.$broadcast('popout-overlay:change-content', response);
                    });

//                    $.ajax({
//                        url: window.location.href + '/'+data.uuid,
//                        method: "GET",
//                        success: function (response) {
//                            console.log(response);
//                        },
//                        error: function (response) {
//                            alert(response);
//                        }
//                    });
                },
                'vuetable:loading' : function(){
                    this.ready = false;
                },
                'vuetable:loaded' : function(){
                    this.ready = true;
                    this.selectedItems = [];
                },
                'vuetable:row-clicked': function (data, event) {
                    if ($.inArray(event.target.type, ['checkbox', 'button']) == -1) {
                        var checkboxEl = $(event.target).closest('tr').find(':checkbox');
                        checkboxEl.trigger('click');
                    }
                },
                'vuetable:load-success': function (response) {
//                    console.log('success: ', response);
                },
                'vuetable:load-error': function (response) {
                    console.log('Load Error: ', response);
                },
                'checkbox:clicked': function (event, item) {
                    var scope = this;
                    setTimeout(function () {
                        var elTr = $(event.target).closest('tr');
                        var elCheckbox = elTr.find(':checkbox');

                        if (elCheckbox.is(':checked')) {
                            elTr.addClass('highlight_row');
                            scope.selectedItems.push(item);
                        } else {
                            elTr.removeClass('highlight_row');
                            scope.selectedItems.$remove(item);
                        }
                    }, 0);
                }
            }
        });

        $(function(){
            $('#navbarSideButton').on('click', function() {
                $('#navbarSide').css({
                    'top' :  $('.app-header').outerHeight()
                }).toggleClass('reveal')
            });
        });
    </script>

@endsection