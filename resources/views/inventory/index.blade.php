@extends('layouts.full', ['contentId' => 'manage_inventory'])

@section('body-menu')

    <div class="pull-left">
        <div class="btn-toolbar center-block text-center">
            <div class="btn-group dropdown ">
                <button :disabled="selectedItems.length == 0 || ! ready"
                        v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"
                        class="disabled btn white btn-sm dropdown-toggle"
                        data-toggle="dropdown">
                    <span class="dropdown-label">Bulk (@{{selectedItems.length}})</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">Delete</a>
                </div>
            </div>

            <a :disabled="selectedItems.length == 0 || ! ready"
               v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"
               data-toggle="modal" data-target="#m-md"
               data-backdrop="static" data-keyboard="false" href="#"
               class="disabled btn_add_selected text-white  btn btn-sm warning">Add Selected
                Items to Sale</a>

            <a :disabled="selectedItems.length == 0 || ! ready"
               v-bind:class="{'disabled': selectedItems.length == 0 || ! ready}"
               data-toggle="modal" data-target="#m-md-fb"
               data-backdrop="static" data-keyboard="false" href="#"
               class="disabled text-white btn_add_selected btn btn-sm warning">Add Selected
                Items to Facebook</a>
        </div>
    </div>

    <div class="pull-right">
        <button class="btn primary btn-sm " id="navbarSideButton">Filter</button>
        <a href="{{ route('shop.inventory.index', [user()->username]) }}" class="btn btn-sm white">Manage</a>
        <a href="{{ route('shop.inventory.create', [user()->username]) }}" class="btn btn-sm white">Add Items</a>
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

    <style>
        .reveal {
            left: 0 !important;;
        }
        .navbar-side {
            -webkit-transform: translateX(0%);
            -ms-transform: translateX(0%);
            transform: translateX(0%);
            -webkit-transition: 300ms ease;
            transition: 300ms ease;
            height: 100% !important;
            width: 350px;
            position: fixed;
            top: 64px;
            left: -350px;
            padding: 0;
            list-style: none;
            background-color: rgba(43,41,56, .95);
            overflow-y: auto;
            z-index: 2000;
        }
        tr.highlight_row td {
            background-color: #fefbf2;
        }

        .table-wrapper {
            position: relative;
        }

        /* Loading Animation: */
        .vuetable-wrapper {
            position: relative;
            opacity: 1;
        }

        .loader {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s linear;
            width: 200px;
            height: 30px;
            font-size: 1em;
            text-align: center;
            margin-left: -100px;
            letter-spacing: 4px;
            color: #f77a99;
            position: absolute;
            top: 20px;
            left: 50%;
        }

        .loading .loader {
            visibility: visible;
            opacity: 1;
            z-index: 100;
        }

        .loading .vuetable {
            opacity: 0.3;
            filter: alpha(opacity=30);
        }

        span.multiselect-native-select {
            position: relative
        }

        span.multiselect-native-select select {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px -1px -1px -3px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            left: 50%;
            top: 30px
        }

        .multiselect-container {
            position: absolute;
            list-style-type: none;
            margin: 0;
            padding: 0
        }

        .multiselect-container .input-group {
            margin: 5px
        }

        .multiselect-container > li {
            padding: 0
        }

        .multiselect-container > li > a.multiselect-all label {
            font-weight: 700
        }

        .multiselect-container > li.multiselect-group label {
            margin: 0;
            padding: 3px 20px 3px 20px;
            height: 100%;
            font-weight: 700
        }

        .multiselect-container > li.multiselect-group-clickable label {
            cursor: pointer
        }

        .multiselect-container > li > a {
            padding: 0
        }

        .multiselect-container > li > a > label {
            margin: 0;
            height: 100%;
            cursor: pointer;
            font-weight: 400;
            padding: 3px 20px 3px 10px
        }

        .multiselect-container > li > a > label.radio,
        .multiselect-container > li > a > label.checkbox {
            margin: 0
        }

        .multiselect-container > li > a > label > input[type=checkbox] {
            margin-bottom: 5px
        }

        .btn-group > .btn-group:nth-child(2) > .multiselect.btn {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px
        }

        .form-inline .multiselect-container label.checkbox,
        .form-inline .multiselect-container label.radio {
            padding: 3px 20px 3px 40px
        }

        .form-inline .multiselect-container li a label.checkbox input[type=checkbox],
        .form-inline .multiselect-container li a label.radio input[type=radio] {
            margin-left: -20px;
            margin-right: 0
        }
    </style>

    @{{ $refs.vuetable.tablePagination }}
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

    <script>
        Vue.component('dropdown', {
            template: ['' +
            '<div class="dropdown pull-right m-r-1">' +
                '<button class="btn btn-xs white dropdown-toggle" data-toggle="dropdown"></button>' +
                '<div class="dropdown-menu dropdown-menu-right text-sm"> ' +
                    '<a class="dropdown-item" href="javascript:;" v-on:click="previewItem(rowData)">View</a> ' +
                    '<a class="dropdown-item" href="javascript:;" v-on:click="editItem(rowData)">Edit</a> ' +
                    '<a class="dropdown-item" href="javascript:;" v-on:click="deleteItem(rowData)">Delete</a> ' +
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
                paginationComponent: 'vuetable-pagination',
                tablePagination: null,
                paginationInfoTemplate : null,
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
                        name: 'item',
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
                viewProfile: function (id) {
                    console.log('view profile with id:', id)
                },
                rowClassCB: function (data, index) {
                    return 'p-l-0 m-l-0';
                },
                addItemsToSale: function(){
                    var inventory_id = [];
                    var flashsale_id = [];

                    var fsEl = $('.flashsale_id_el:checked');

                    $.each(fsEl, function(i,k){
                        flashsale_id.push($(this).val());
                    });

                    $.each(this.selectedItems, function(i,k){
                        inventory_id.push(this.id);
                    });

                    $.ajax({
                        data: {
                            inventory_id: inventory_id,
                            flashsale_id: flashsale_id
                        },
                        dataType: "json",
                        url: "{{ apiRoute('inventory.associate.store', [user()->username]) }}",
                        method: "POST",
                        success: function (response) {

                        },
                        error: function () {
                            alert('An error occurred, please try again.');
                        }
                    });
                }
            },
            events: {
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
                'vuetable:action': function (action, data) {
                    console.log('vuetable:action', action, data);
                },
                'vuetable:load-success': function (response) {
                    console.log('success: ', response);
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