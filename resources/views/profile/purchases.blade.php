@extends('layouts.full')

@section('body-content')
    <div class="box">
        <div class="box-header">
            <h4>Pending claims and purchases</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div id="claims__wrapper">
                <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white m-b-0">
                    <thead>
                    <tr class="  ">
                        <th>Item</th>
                        <th>Claim Status</th>
                        <th>Price</th>
                        <th>Seller</th>
                        <th>Date</th>
                        {{--<th>State</th>--}}
                        <th>Shipping Status</th>
                        <th></th>
                    </tr>
                    </thead>
            <tbody>
            @foreach($claims as $claim)
                <tr class="{{ $claim->wasRejected() ? ' claim-rejected ' : null }}">
                    <td>
                        <div class="avatar-thumbnail-container">
                            <div class="avatar-thumbnail _32">
                                <img src="{{ $claim->listedItem->cover_photo->location }}">
                            </div>
                            <span>{{ $claim->listedItem->title }}</span>
                        </div>
                    </td>
                    <td>{!! $claim->present()->getClaimStatus()  !!}</td>
                    <td>{{ currency($claim->price) }}</td>
                    <td>{{ $claim->listedItem->owner->username }}</td>
                    <td>{{ $claim->createdAtHumanNoTime() }} <i data-placement="top" class="fa fa-clock-o" data-toggle="tooltip" title="{{ $claim->created_at->format('g:i A') }}"></i></td>
                    {{--<td>--}}
                        {{--<span class="pending-status">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>--}}
                    {{--</td>--}}
                    <td>{!! $claim->present()->getShippingStatus($statusAsBuyerPov = true) !!}</td>
                    <td><a class="btn btn-xs white" href="{{ route('profile.purchases.show', [$claim->getUUID()]) }}">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
            </div>
        </div>
    </div>

@endsection