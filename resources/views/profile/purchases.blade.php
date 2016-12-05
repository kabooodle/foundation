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
                        <th></th>
                        <th class="text-muted">Item</th>
                        <th class="text-muted p-l-0 m-l-0">Price</th>
                        <th class="text-muted p-l-0 m-l-0">Seller</th>
                        <th class="text-muted p-l-0 m-l-0">Claimed On</th>
                        <th class="text-muted p-l-0 m-l-0">State</th>
                        <th class="text-muted p-l-0 m-l-0">Shipping Status</th>
                        <th></th>
                    </tr>
                    </thead>
            <tbody>
            @foreach($claims as $claim)
                <tr>
                    <td></td>
                    <td style="vertical-align: middle !important">
                        <a href="{{ route('profile.purchases.show', $claim->getUUID()) }}"
                           class="_500 h6"><span class="@if($claim->wasRejected()) w-24 @else w-40 @endif avatar">
                                            <img src="{{ $claim->inventoryItem->firstImage() ? $claim->inventoryItem->firstImage()->location : 'https://placekitten.com/g/30/30' }}">
                                          </span></a>
                    </td>
                    <td style="vertical-align: middle !important">${{ $claim->price }}</td>
                    <td style="vertical-align: middle !important">{{ $claim->inventoryItem->owner->name }}</td>
                    <td style="vertical-align: middle !important">{{ $claim->created_at->format('F jS \\a\\t g:i A') }}</td>
                    <td style="vertical-align: middle !important">
                        <span class="pending-status">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>
                    </td>
                    <td style="vertical-align: middle !important">{!! $claim->present()->getShippingStatus() !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
            @if($claim->perPage != 0)
            <div class="box-footer">
                <div class="">
                    <p>{{ $claim->perPage }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection