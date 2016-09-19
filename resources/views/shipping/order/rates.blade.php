@extends('layouts.full')


@section('body-menu')

    @include('shipping.order.partials._bodynav')

@endsection


@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"/>
    @endpush

    <div class="alert warning">
        <p class="m-b-0 text-center h6">Note: Rates expire {{ $shipment->expires_on->diffForHumans() }}.</p>
    </div>

    <div class="box white" id="shipping_rates_wrapper">
        <div class="box-header">
            <h4>Available Shipping Rates</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Carrier</th>
                        <th>Service Name</th>
                        <th>*Estimated Transmit Time</th>
                        <th>Cost</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody is="rates-component">

                </tbody>

                    {{--@foreach($rates as $rate)--}}
                    {{--<tr>--}}
                        {{--<td><img src="{{ $rate->getCarrierLogos()['small'] }}" alt="{{ $rate->getProvider() }}"></td>--}}
                        {{--<td>{{ $rate->getServiceLevelName() }}</td>--}}
                        {{--<td>{{ $rate->getDays() }} {{ str_plural('day', $rate->getDays()) }}</td>--}}
                        {{--<td>${{ $rate->getAmount() }}</td>--}}
                        {{--<td class="pull-right">--}}
                            {{--<button type="button" v-on:click="purchaseLabel" class="btn btn-xs success" data-uuid="{{ $rate->getRateId() }}">--}}
                                {{--Purchase Label--}}
                            {{--</button>--}}
                        {{--</td>--}}
                    {{--</tr>--}}
                    {{--@endforeach--}}

            </table>
        </div>
    </div>



    <div class="row">
        <div class="col-md-4">
            <div class="box">
                <div class="box-header">
                    <h4 class="m-b-0">Parcel</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    {{ $shipment->getMeasurements() }}
                    <br>
                    {{ $shipment->parcel_data['weight'] }} {{ $shipment->parcel_data['mass_unit'] }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box">
                <div class="box-header">
                    <h4 class="m-b-0">Recipient Address</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <address class="m-b-0">
                        <span class="_500 block">{{ $shipment->recipient_data->name }}</span>
                        <span class="block">{{ $shipment->recipient_data->street1 }}</span>
                        @if($shipment->recipient_data->street2)
                            <span class="block">{{ $shipment->recipient_data->street2 }}</span>
                        @endif
                        <span class="block">{{ $shipment->recipient_data->city }}, {{ $shipment->recipient_data->state }}, {{ $shipment->recipient_data->zip }}</span>
                        <a href="mailto:{{ $shipment->recipient_data->email }}">{{ $shipment->recipient_data->email }}</a>
                        @if($shipment->recipient_data->phone)
                            <span class="block">{{ $shipment->recipient_data->phone }}</span>
                        @endif
                    </address>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box">
                <div class="box-header">
                    <h4 class="m-b-0">Sender Address</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <address class="m-b-0">
                        <span class="_500 block">{{ $shipment->sender_data->name }}</span>
                        <span class="block">{{ $shipment->sender_data->street1 }}</span>
                        @if($shipment->sender_data->street2)
                            <span class="block">{{ $shipment->sender_data->street2 }}</span>
                        @endif
                        <span class="block">{{ $shipment->sender_data->city }}, {{ $shipment->sender_data->state }}, {{ $shipment->sender_data->zip }}</span>
                        <a href="mailto:{{ $shipment->sender_data->email }}">{{ $shipment->sender_data->email }}</a>
                        @if($shipment->sender_data->phone)
                            <span class="block">{{ $shipment->sender_data->phone }}</span>
                        @endif
                    </address>
                </div>
             </div>
        </div>
    </div>

    <div class="m-t-3 p-t-3">
        <small class="font-italic">*Transit time is a non-guaranteed average. The number of days is the best estimate available. Some carriers guarantee this delivery time.</small>
    </div>

    <script id="rates-template" type="text/x-template">
        <tbody>
        <tr v-for="rate in rates">
            <td><img :src="rate.provider_image_75"  alt="@{{ rate.provider }}" title="@{{ rate.provider }}"></td>
            <td>@{{ rate.servicelevel_name }}</td>
            <td>@{{ rate.days }} @{{ parseInt(rate.days) |  pluralize "day" }}</td>
            <td>$@{{ rate.amount }}</td>
            <td class="pull-right">
                <button type="button" v-on:click="purchaseLabel" data-route="{{ route('shipping.transactions.store', [$shipment->uuid]) }}"  class="btn btn-xs success btn-purchase-label-el" data-uuid="@{{  rate.object_id }}">
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

                    noty({
                        text: 'Confirm that you wish to proceed with purchase.',
                        layout: 'center',
                        theme: 'relax',
                        type: 'alert',
                        modal: true,
                        animation: {
                            open: {height: 'toggle'},
                            close: {height: 'toggle'},
                            easing: 'linear',
                            speed: 1
                        },
                        timeout: 9000,
                        buttons: [
                            {
                                addClass: 'btn btn-sm primary', text: 'Confirm Purchase', onClick: function ($noty) {
                                    $(this).addClass('disabled').prop('disabled', true);
                                scope.$http.post($this.data('route'), {rate : $this.data('uuid')} ).then(function(data, x){
                                    if (data.body.redirect && data.body.redirect.length) {
                                        window.location.href = data.body.redirect;
                                    }
                                }, function(data){
                                    $noty.close();
                                    scope._togglePurchaseLabelBtns($this);
                                });
                            }
                            },
                            {
                                addClass: 'btn btn-link btn-sm', addId: 'noty_cancel', text: 'Cancel', onClick: function ($noty) {
                                $noty.close();
                                scope._togglePurchaseLabelBtns($this);
                            }
                            }
                        ]
                    });

                    return false;
                },
                _togglePurchaseLabelBtns: function($el) {
                    var $els = $('.btn-purchase-label-el');
                    if ($els.is(':disabled')) {
                        $els.removeClass('disabled').prop('disabled', false);
                        $els.removeClass('white text-muted').addClass('success').css('text-decoration', 'none');
                    } else {
                        $els.addClass('disabled').prop('disabled', true);
                        $els.not($el).addClass('text-muted').removeClass('success').css('text-decoration', 'line-through');
                    }
                }
            }
        });

        Vue.component('rates-component', RatesComponent);
    </script>

@endsection