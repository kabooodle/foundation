@extends('layouts.body_w_leftnav')

@section('body-menu')

    <div class="btn-toolbar center-block text-center">
        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Filter</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Active</a>
                <a class="dropdown-item" href="">Archived</a>
            </div>
        </div>
    </div>

@endsection


@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-right m-l">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">people</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">6,000 <span class="text-sm">Members</span></a></h4>
                    <small class="text-muted">632 activities post.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box-color p-a ">
                <div class="pull-right m-l">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">local_shipping</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">40 <span class="text-sm">Orders</span></a></h4>
                    <small class="text-muted">38 Shipped.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a ">
                <div class="pull-left m-r">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">comment</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">69 <span class="text-sm">Shipping Labels Used</span></a></h4>
                    <small class="text-muted">5 remaining.</small>
                </div>
            </div>
        </div>
    </div>

    <div id="claims__wrapper">
        <table class="table table-condensed table-as-list white">
            <thead>
            <tr class="  ">
                <th></th>
                <th class="text-muted">Item</th>
                <th class="text-muted p-l-0 m-l-0">Claim Price</th>
                <th class="text-muted p-l-0 m-l-0">Claimer</th>
                <th class="text-muted p-l-0 m-l-0">Claimed On</th>
                <th class="text-muted p-l-0 m-l-0">Accepted/Rejected On</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $claim)
                @include('inventory.claims.partials._claimrow')
            @endforeach
            </tbody>
        </table>


        @include('inventory.claims.partials._actionmodal')
    </div>

    <script>
        var vModal = new Vue({
            el: '#claims__wrapper',
            data: {
                modal_route: null,
                modal_action: null,
                claim_uuid: null
            },
            methods: {
                toggleActionModal: function (e) {
                    e.preventDefault();
                    var $el = $(e.target);
                    this.claim_uuid = $el.data('id');
                    this.modal_route = $el.data('route');
                    this.modal_action = $el.data('method');
                },
                submitModal: function (e) {
                    e.preventDefault();
                    var $el = $(e.target);
                    var that = this; // the vue parent reference
                    var formData = $el.closest('.modal-content').find('form').serialize();
                    this._disableModalBtns($el);
                    $.ajax({
                        url: this.modal_route,
                        type: this.modal_action,
                        data: formData,
                        dataType: "json"
                    })
                            .done(function (json) {
                                if (json.html) {
                                    $('tr[data-claim-id='+that.claim_uuid+']').replaceWith(json.html);
                                }
                            })
                            .fail(function (xhr, status, errorThrown) {
                                alert(xhr.responseJSON.message);
                            })
                            .always(function(){
                                that._enableModalBtns($el);
                                $el.closest('.modal').modal('hide');
                                $el.closest('.modal').find('input, select, textarea').val('');
                                that.claim_uuid = null;
                            });
                },
                _disableModalBtns: function($el)
                {
                    $el.parent().find('.btn').addClass('disabled').attr('disabled',true);
                },
                _enableModalBtns: function($el)
                {
                    $el.parent().find('.btn').removeClass('disabled').attr('disabled',false);
                }
            }
        });

        $(function(){
            $('.datetimepicker').datetimepicker({
                format: "MM/DD/YYYY hh:mmA",
                icons: {
                    time: 'fa fa-clock-o',
                    date: "fa fa-calendar",
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right'
                }
            });
        });
    </script>

@endsection