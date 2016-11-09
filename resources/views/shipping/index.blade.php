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
                        <th>Claims</th>
                        <th>Claimer</th>
                        <th>Tracking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($shipments as $shipment)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{{ $shipment->shipment->claims->count() }}</td>
                        <td>{{ $shipment->shipment->claimer->name }}</td>
                        <td>{{ $shipment->tracking_number }}</td>
                        <td><span class="label">Unknown</span></td>
                        <td><button class="btn btn-xs white">Label</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>



@endsection