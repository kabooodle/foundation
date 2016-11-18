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
    <div id="claims__wrapper">
        <div class="box white">
            <div class="box-header">
                <h2>Claims</h2>
            </div>
            <div class="box-divider m-a-0"></div>

        <table class="table table-condensed table-as-list white">
            <thead>
                <tr>
                    <th></th>
                    <th class="text-muted">Item</th>
                    <th class="text-muted p-1-0 m-1-0">Price</th>
                    <th class="text-muted p-1-0 m-1-0">Seller</th>
                    <th class="text-muted p-1-0 m-1-0">Claimed On</th>
                    <th class="text-muted p-1-0 m-1-0">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($claims as $claim)
                <tr>
                    <td></td>
                    <td style="vertlical-align: middle !important">
                        <a href="{{ route('profile.purchases.show', [$claim->id]) }}"
                           class="_500 h6"><span class="@if($claim->wasRejected()) w-24 @else w-40 @endif avatar">
                                            <img src="{{ $claim->inventoryItem->firstImage() ? $claim->inventoryItem->firstImage()->location : 'https://placekitten.com/g/30/30' }}">
                                          </span></a>
                    </td>
                    <td style="vertlical-align: middle !important">${{ $claim->price }}</td>
                    <td style="vertlical-align: middle !important">{{ $claim->inventoryItem->owner->name }}</td>
                    <td style="vertlical-align: middle !important">{{ $claim->created_at->diffForHumans() }}</td>
                    <td style="vertlical-align: middle !important">
                        <span class="pending-status">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>

    <div class="box white">
        <div class="box-header">
            <h2>Purchases</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="row-col m-b">
            <div class="col-md-6">
                <div class="box no-shadow white p-a">
                    <p>
                        @foreach($claims as $claim)
                            {{ $claim->inventory_item_object_data['name'] }}
                            {{ $claim->inventory_item_object_data['description'] }}
                            {{ $claim->inventory_item_object_data['user_id'] }}
                            {{ $claim->price }}
                            {{ $claim }}

                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection