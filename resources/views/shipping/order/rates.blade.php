@extends('layouts.full')


@section('body-menu')

    @include('shipping.order.partials._bodynav')

@endsection


@section('body-content')

    <div class="box white" id="shipping_rates_wrapper">
        <div class="box-header">
            <h4>Available Shipping Rates</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th>Carrier</th>
                        <th>Service Name</th>
                        <th>*Transit Time</th>
                        <th>Cost</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody is="rates-component">

                </tbody>
            </table>
        </div>
    </div>



    @include('shipping.order.partials._shipmentfooter', ['shipment' => $shipment])

    <div class="m-t-3 p-t-3">
        <small class="font-italic">*Transit time is a non-guaranteed average. The number of days is the best estimate available. Some carriers guarantee this delivery time.</small>
    </div>

    <script id="rates-template" type="text/x-template">
        <tbody>
        <tr v-for="rate in rates">
            <td><img
                        :src="rate.shippoRateObject.provider_image_75"
                        :alt="rate.provider"
                        :title="rate.provider"></td>
            <td>@{{ rate.shippoRateObject.servicelevel_name }}</td>
            <td>@{{ rate.days }} days <small class="block text-muted">@{{ rate.durationTerms}}</small></td>
            <td>$@{{ rate.adjustedTotalAmount }}</td>
            <td class="pull-right">
                <button type="button" v-on:click="purchaseLabel"
                        data-route="{{ route('merchant.shipping.transactions.store', [$shipment->uuid]) }}"
                        class="btn btn-xs white btn-purchase-label-el"
                        :data-uuid="rate.shippoRateObject.object_id">
                    Purchase Label
                </button>
            </td>
        </tr>
        </tbody>
    </script>


    <script>

        var RatesComponent = Vue.extend({
            template: '#rates-template',
            data : function() {
                return {
                    rates : {!!  $shipment->rates_as_raw  !!}
                }
            },
            methods : {
                purchaseLabel: function(e){
                    e.preventDefault();
                    var scope = this,
                            $this = $(e.target);

                    scope._togglePurchaseLabelBtns($this);

                    confirmModal(function(){
                        $('.noty-btn').addClass('disabled').prop('disabled', true);
                        $('.noty-btn-primary').html('<i class="fa fa-spin fa-spinner"></i>');
                        scope.$http.post($this.data('route'), {rate : $this.data('uuid')} ).then(function(data, x){
                            if (data.body.redirect && data.body.redirect.length) {
                                window.location.href = data.body.redirect;
                            }
                        }, function(response){
                            $.noty.closeAll();
                            scope._togglePurchaseLabelBtns($this);
                            notify({'text' : response.body.error});
                        });
                    }, function($noty){
                        $noty.close();
                        scope._togglePurchaseLabelBtns($this);
                    });

                    return false;
                },
                _togglePurchaseLabelBtns: function($el) {
                    var $els = $('.btn-purchase-label-el');
                    if ($els.is(':disabled')) {
                        $els.removeClass('disabled').prop('disabled', false);
                        $els.removeClass('text-muted').closest('tr').css('text-decoration', 'none').removeClass('text-muted');
                    } else {
                        $els.addClass('disabled').prop('disabled', true);
                        $els.not($el).addClass('text-muted').closest('tr').css('text-decoration', 'line-through').addClass('text-muted');
                    }
                }
            }
        });

        Vue.component('rates-component', RatesComponent);

        new Vue({
            el : '#shipping_rates_wrapper'
        });

    </script>

@endsection