@extends('layouts.full', ['contentId' => 'shipping_index'])


@section('body-menu')
    @include('shipping.order.partials._bodynav')
@endsection


@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Shipping Transactions</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th></th>
                        <th>Claimer</th>
                        <th>Items</th>
                        <th>Cost</th>
                        <th>Date</th>
                        {{--<th>Tracking</th>--}}
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($shipments as $shipment)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{{ $shipment->shipment->claimer->name }}</td>
                        <td>{{ $shipment->shipment->claims->count() }}</td>
                        <td>${{ $shipment->rate_amount }}</td>
                        <td><time datetime="{{ $shipment->createdAtHuman() }}">{{ $shipment->createdAtHumanNoTime() }} <i data-toggle="tooltip" title="{{ $shipment->createdAtHuman() }}" data-placement="top" class="fa fa-clock-o" aria-hidden="true"></i></time></td>
                        {{--<td><a class="text-primary" href="{{ $shipment->tracking_url_provider }}" target="_blank" >{{ $shipment->tracking_number }}</a> <i class="fa fa-external-link" aria-hidden="true"></i></td>--}}
                        <td>{!! $shipment->present()->getStatus()  !!}</td>
                        <td>
                            <div class="pull-right">
                                <a href="{{ route('shipping.transactions.show', [$shipment->shipping_shipments_uuid, $shipment->uuid]) }}"  class="btn btn-xs white">View</a>
                                <a target="_blank" href="{{ route('shipping.transactions.label.show', [$shipment->shipping_shipments_uuid, $shipment->uuid]) }}" class="btn btn-xs white">Shipping Label</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection