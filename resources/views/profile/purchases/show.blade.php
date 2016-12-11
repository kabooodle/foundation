@extends('layouts.full')

@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Claim Information</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white m-b-0">
                <thead>
                    <tr>
                        <th>Seller Name</th>
                        <th>Seller Email</th>
                        <th>Price</th>
                        <th>Claimed On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td >
                        {{--<span class="w-40 avatar"><img src="https://placekitten.com/g/32/32"></span>--}}
                        {{ $claim->inventoryItem->owner->name }}</td>
                    <td ><a class="text-primary" href="mailto:{{ $claim->inventoryItem->owner->email }}">{{ $claim->inventoryItem->owner->email }}</a></td>
                    {{--<td style="border: none !important;">{{ $claim->inventoryItem->owner->shipFromAddress ? $claim->inventoryItem->owner->shipFromAddress->city : null }}</td>--}}
                    <td >${{ $claim->price }}</td>
                    <td >{{ $claim->created_at->format('F jS \\a\\t g:i A') }}</td>
                    <td>{{ $claim->claim_status }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($claim->accepted)
    <div class="box">
        <div class="box-header">
            <h4>Shipping Information</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            @if($claim->shippedManually())
                <p class="m-b-0">Seller indicated this item was shipped manually.</p>
            @else
                @if($transaction = $claim->shipmentTransaction())
                    <div class="box  p-a">
                        <div class="row">
                            <ul class="timeline" id="timeline">

                                @include('shipping.order.partials._shipping_status_item', [
                                    '_itemStatus' => null,
                                    '_itemTimestamp' => null,
                                    '_itemName'=>'Shipment Created'
                                ])

                                @include('shipping.order.partials._shipping_status_item', [
                                    '_itemStatus' => null,
                                    '_itemTimestamp' => null,
                                    '_itemName'=>'With Carrier'
                                ])

                                @include('shipping.order.partials._shipping_status_item', [
                                    '_itemStatus' => null,
                                    '_itemTimestamp' => null,
                                    '_itemName'=>'In Transit'
                                ])

                                @include('shipping.order.partials._shipping_status_item', [
                                    '_itemStatus' => null,
                                    '_itemTimestamp' => null,
                                    '_itemName'=>'Delivered'
                                ])
                            </ul>
                        </div>
                    </div>

                    <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white m-b-0">
                        <thead>
                        <tr>
                            <th >Carrier</th>
                            <th >Service Name</th>
                            <th >*Transit Time</th>
                            <th >Tracking</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><img src="{{ $transaction->rate_data['carrierLogos']['small'] }}" alt="{{ $transaction->rate_data['provider'] }}"></td>
                            <td>{{ $transaction->rate_data['serviceLevelName'] }}</td>
                            <td>{{ $transaction->rate_data['shippoRateObject']['days'] }} days</td>
                            <td><a class="text-primary" href="{{ $transaction->tracking_url_provider }}" target="_blank" >{{ $transaction->tracking_number }}</a> <i class="fa fa-external-link" aria-hidden="true"></i></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                @else
                    <p class="m-b-0">Seller has not yet entered shipping information.</p>
                @endif
            @endif
        </div>
    </div>
    @endif

    @include('inventory.partials._show', [
  'item' => $claim->inventoryItem,
  '_price' => $claim->price
  ])

@endsection