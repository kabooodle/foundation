@extends('layouts.full')


@section('body-menu')

    @include('shipping.order.partials._bodynav')

@endsection


@section('body-content')

    <style>
        .timeline {
            list-style-type: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .li {
            transition: all 200ms ease-in;
        }

        .timestamp {
            margin-bottom: 20px;
            padding: 0 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: .75rem;
        }

        .status {
            padding: 0 70px;
            display: flex;
            justify-content: center;
            border-top: 2px solid #D6DCE0;
            position: relative;
            transition: all 200ms ease-in;
        }
        .status h4 {
            font-weight: 300;
            font-size: 1.1rem;
            margin-top: 20px;
        }
        .status:before {
            content: "";
            width: 25px;
            height: 25px;
            background-color: white;
            border-radius: 25px;
            border: 1px solid #ddd;
            position: absolute;
            margin-left: -13px;
            top: -15px;
            left: 50%;
            transition: all 200ms ease-in;
        }

        .li.complete .status {
            border-top: 2px solid #66DC71;
        }
        .li.complete .status:before {
            background-color: #66DC71;
            border: none;
            transition: all 200ms ease-in;
        }
        .li.complete .status h4 {
            color: #66DC71;
        }

        .li.incomplete {
            color: inherit!important;
            opacity: .6;
        }

        @media (min-device-width: 320px) and (max-device-width: 700px) {
            .timeline {
                list-style-type: none;
                display: block;
            }

            .li {
                transition: all 200ms ease-in;
                display: flex;
                width: inherit;
            }

            .timestamp {
                width: 100px;
            }

            .status:before {
                left: -8%;
                top: 30%;
                transition: all 200ms ease-in;
            }
        }


    </style>


    <div class="box  p-a">
        <div class="row">
                <ul class="timeline" id="timeline">
                    <li class="li">
                        <div class="timestamp">
                        <span class="date">11/05/2016<span>
                        </div>
                        <div class="status">
                            <h4> Received </h4>
                        </div>
                    </li>
                    <li class="li incomplete">
                        <div class="timestamp">
                    <span class="date">Pending<span>
                        </div>
                        <div class="status">
                            <h4> In Transit </h4>
                        </div>
                    </li>
                    <li class="li incomplete">
                        <div class="timestamp">
                    <span class="date">Pending<span>
                        </div>
                        <div class="status">
                            <h4> Delivered </h4>
                        </div>
                    </li>
                </ul>
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
                        <td>${{ $transaction->rate_amount }}</td>
                        <td><a class="text-primary" href="{{ $transaction->tracking_url_provider }}" target="_blank" >{{ $transaction->tracking_number }}</a></td>
                        <td><a href="{{ $transaction->label_url }}" target="_blank" class="btn btn-xs white ">Get Shipping Label</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>



    @include('shipping.order.partials._shipmentfooter', ['shipment' => $transaction->shipment])

@endsection