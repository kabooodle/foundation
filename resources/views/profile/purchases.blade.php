@extends('layouts.full')

@section('body-menu')

    <div class="btn-toolbar center-block text-center">
        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Filter</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Claimed</a>
                <a class="dropdown-item" href="">Purchased</a>
            </div>
        </div>
    </div>

@endsection

@section('body-content')
    <div class="box">
        <div class="box-header">
            <h4>Pending claims and purchases</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div id="claims__wrapper">
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr class="  ">
                        <th>Item</th>
                        <th>Claim Status</th>
                        <th>Price</th>
                        <th>Seller</th>
                        <th>Date</th>
                        <th>State</th>
                        <th>Shipping Status</th>
                        <th></th>
                    </tr>
                    </thead>
            <tbody>
            @foreach($claims as $claim)
                <tr>
                    <td>
                        <div class="avatar-thumbnail-container">
                            <div class="avatar-thumbnail _32">
                                @if($claim->inventoryItem->firstImage())
                                    <img src="{{ $claim->inventoryItem->firstImage()->location }}">
                                @endif
                            </div>
                            <span><a class="text-primary" href="{{ route('profile.purchases.show', [$claim->getUUID()]) }}">{{ $claim->inventoryItem->name_with_variant }}</a></span>
                        </div>
                    </td>
                    <td>{{ $claim->claim_status }}</td>
                    <td>${{ $claim->price }}</td>
                    <td>{{ $claim->inventoryItem->owner->name }}</td>
                    <td>{{ $claim->created_at->format('F jS \\a\\t g:i A') }}</td>
                    <td>
                        <span class="pending-status">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>
                    </td>
                    <td>{!! $claim->present()->getShippingStatus() !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

@endsection