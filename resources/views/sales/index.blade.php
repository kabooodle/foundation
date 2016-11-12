@extends('layouts.full', ['contentId' => 'sales_index'])


@section('body-menu')
    <div class="pull-left">
        <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter Sales</button>
    </div>

@endsection


@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Accepted Sales</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Accepted on</th>
                    <th>Claimer</th>
                    <th>Shipping Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td><input type="checkbox" class="shipment_checkbox"></td>
                        <td>
                            <div class="avatar-thumbnail-container">
                                <div class="avatar-thumbnail _32">
                                    <img src="{{ $sale->inventoryItem->firstImage()->location }}">
                                </div>
                                <span>{{ $sale->inventoryItem->name_with_variant }}</span>
                            </div>
                        </td>
                        <td>${{ $sale->accepted_price ? : $sale->price }}</td>
                        <td>{{ $sale->humanizeNoTime($sale->accepted_on) }}</td>
                        <td>{!! $sale->claimer->name !!}</td>
                        <td>@if($sale->shipmentTransaction()) {!! $sale->shipmentTransaction()->present()->getStatus()  !!} @else Accepted @endif</td>
                        <td>
                            <div class="pull-right">
                            @if($sale->shipmentTransaction())
                                <a class="btn white btn-xs" href="{{ route('shipping.transactions.show', [$sale->shipmentTransaction()->shipping_shipments_uuid, $sale->shipmentTransaction()->uuid]) }}">Track Shipping</a>
                            @else
                                <div class="dropdown">
                                    <button
                                            class="btn white btn-xs dropdown-toggle"
                                            type="button"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        Shipping Options
                                    </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">Create Label</a>
                                            <a class="dropdown-item" href="#">Manually Ship</a>
                                        </div>
                                </div>
                            @endif
                            </div>
                        </td>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix">
        <div class="pull-right">
            {{ $sales->links() }}
        </div>
    </div>

@endsection
