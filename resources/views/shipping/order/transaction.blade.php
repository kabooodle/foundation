@extends('layouts.full')


@section('body-menu')

    @include('shipping.order.partials._bodynav')

@endsection


@section('body-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"/>
    @endpush

    <div class="box">
        <div class="box-header">
            <h4>Shipping Information</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <a href="{{ $transaction->label_url }}" target="_blank" class="btn white">Get Shipping Label</a>
            <a href="{{ $transaction->tracking_url_provider }}" target="_blank" class="text-primary">Carrier Tracking Link</a>
            <p>Tracking Number: {{ $transaction->tracking_number }}</p>
            <a href=""></a>
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


@endsection