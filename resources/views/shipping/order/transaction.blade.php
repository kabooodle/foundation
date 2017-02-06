@extends('layouts.full')


@section('body-menu')

    @include('shipping.order.partials._bodynav')

@endsection


@section('body-content')

    <div class="box  p-a">
        <div class="row">
                @include('shipping.partials._timeline', ['transaction' => $transaction ])
        </div>
    </div>


    <div class="box">
        <div class="box-header">
            <h4>Shipping Information</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th class="text-muted">Carrier</th>
                        <th class="text-muted">Service Name</th>
                        <th class="text-muted">*Transit Time</th>
                        <th class="text-muted">Cost</th>
                        <th class="text-muted">Tracking</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="{{ $transaction->rate_data['carrierLogos']['small'] }}" alt="{{ $transaction->rate_data['provider'] }}"></td>
                        <td>{{ $transaction->rate_data['serviceLevelName'] }}</td>
                        <td>{{ $transaction->rate_data['shippoRateObject']['days'] }} days</td>
                        <td>${{ $transaction->rate_final_amount }}</td>
                        <td><a class="text-primary" href="{{ $transaction->tracking_url_provider }}" target="_blank" id="link-text">{{ $transaction->tracking_number }}</a>
                        <a href="javascript:;" data-animation="false" data-clipboard-target="#link-text"><i class="fa fa-clipboard" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <div class="pull-right">
                                <a target="_blank" href="{{ route('merchant.shipping.transactions.label.show', [$transaction->shipping_shipments_uuid, $transaction->uuid]) }}" class="btn btn-xs white">Shipping Label</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    @include('shipping.order.partials._shipmentfooter', ['shipment' => $transaction->shipment])

@endsection