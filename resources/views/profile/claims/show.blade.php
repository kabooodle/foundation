@extends('layouts.full', ['contentId' => 'claims_page'])

@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Claim Information
                <span class="pull-right" style="position: relative; top: -5px;">
                    <cancel-claim
                        :claim="{{ json_encode($claim) }}"
                        claim-endpoint="{{ apiRoute('users.claims.show', [webUser()->username, $claim->id]) }}"
                    ></cancel-claim>
                </span>
            </h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white m-b-0">
                <thead>
                <tr>
                    <th>Seller Username</th>
                    <th>Seller Email</th>
                    <th>Price</th>
                    <th>Claimed On</th>
                    <th>Sale</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td >
                        {{--<span class="w-40 avatar"><img src="https://placekitten.com/g/32/32"></span>--}}
                        {{ $claim->listable->owner->username }}</td>
                    <td ><a class="text-primary" href="mailto:{{ $claim->listable->owner->email }}">{{ $claim->listable->owner->email }}</a></td>
                    <td >{{ currency($claim->price) }}</td>
                    <td >{{ $claim->createdAtHumanNoTime() }} <i data-placement="top" class="fa fa-clock-o" data-toggle="tooltip" title="{{ $claim->created_at->format('g:i A') }}"></i></td>
                    <td>{{ $claim->listingItem->sale_name }}</td>
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
                                @include('shipping.partials._timeline', ['transaction' => $transaction ])
                            </div>
                        </div>

                        <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white m-b-0">
                            <thead>
                            <tr>
                                <th >Carrier</th>
                                <th >Service Name</th>
                                <th >*Transit Time</th>
                                <th >Tracking</th>
                                <th>Origin</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><img src="{{ $transaction->rate_data['carrierLogos']['small'] }}" alt="{{ $transaction->rate_data['provider'] }}"></td>
                                <td>{{ $transaction->rate_data['serviceLevelName'] }}</td>
                                <td>{{ $transaction->rate_data['shippoRateObject']['days'] }} days</td>
                                <td><a class="text-primary" href="{{ $transaction->tracking_url_provider }}" target="_blank" id="link-text">{{ $transaction->tracking_number }}</a>
                                    <a href="javascript:;" data-animation="false" data-clipboard-target="#link-text"><i class="fa fa-clipboard" aria-hidden="true"></i></a>
                                </td>
                                <td>{{ $transaction->shipment->getSenderOrigin() }}</td>
                                <td>{!! $transaction->present()->getStatus()  !!}</td>
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

    @include('listables.partials._show', [
        'listable' => $claim->listable,
        '_price' => $claim->price
    ])

@endsection


@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/claims-show.js') }}"></script>
@endpush