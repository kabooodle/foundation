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

    <div class="box">
        <div class="box-body">
            <div id="claims__wrapper">
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr class="  ">
                        <th></th>
                        <th class="text-muted">Item</th>
                        <th class="text-muted p-l-0 m-l-0">Claim Price</th>
                        <th class="text-muted p-l-0 m-l-0">Claimer</th>
                        <th class="text-muted p-l-0 m-l-0">Claimed On</th>
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

        </div>
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