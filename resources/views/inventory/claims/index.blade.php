@extends('layouts.body_w_leftnav')

@section('body-menu')

        <div class="btn-toolbar center-block text-center">
            <div class="btn-group dropdown">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Filter</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">Active</a>
                    <a class="dropdown-item" href="">Archived</a>
                </div>
            </div>
        </div>

@endsection


@section('body-content')

    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">75 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">6 waiting payment.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-right m-l">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">people</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">6,000 <span class="text-sm">Members</span></a></h4>
                    <small class="text-muted">632 activities post.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box-color p-a ">
                <div class="pull-right m-l">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">local_shipping</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">40 <span class="text-sm">Orders</span></a></h4>
                    <small class="text-muted">38 Shipped.</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a ">
                <div class="pull-left m-r">
            <span class="w-40 dker text-center rounded">
              <i class="material-icons">comment</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">69 <span class="text-sm">Shipping Labels Used</span></a></h4>
                    <small class="text-muted">5 remaining.</small>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-condensed table-as-list white">
        <thead>
        <tr class="  ">
            <th></th>
            <th class="text-muted">Item</th>
            <th class="text-muted p-l-0 m-l-0">Claim Price</th>
            <th class="text-muted p-l-0 m-l-0">Claimer</th>
            <th class="text-muted p-l-0 m-l-0">Claimed On</th>
            <th class="text-muted p-l-0 m-l-0">Accepted/Rejected On</th>
            <th>

            </th>
        </tr>
        </thead>
        <tbody>
    @foreach($data as $claim)
        <tr class=" @if($claim->wasRejected()) text-strikethrough strike text-muted @endif ">
            <td style="vertical-align: middle !important"><div class="_600">@if($claim->isPending()) <i class="fa fa-lg fa-meh-o text-warning" aria-hidden="true"></i> @elseif($claim->wasRejected()) <i class="fa fa-frown-o fa-lg text-danger" aria-hidden="true"></i> @else <i class="fa fa-smile-o fa-lg text-success" aria-hidden="true"></i> @endif </div></td>
            <td style="vertical-align: middle !important">
                <a href="{{ route('shop.inventory.show', [user()->username, $claim->inventoryItem->obfuscateToURIStringFromModel()]) }}"
                   class="_500 h6"><span class="@if($claim->wasRejected()) w-24 @else w-40 @endif avatar">
                                            <img src="https://placekitten.com/g/30/30">
                                          </span></a>
            </td>
            <td style="vertical-align: middle !important">${{ $claim->inventory_item_object_data['price_usd'] }}</td>
            <td style="vertical-align: middle !important">{!!  $claim->claimedBy->name  !!}</td>
            <td style="vertical-align: middle !important">{{ $claim->created_at->diffForHumans() }}</td>
            <td style="vertical-align: middle !important">
                <span class="">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>
            </td>
            <td style="vertical-align: middle !important">
                <div class="pull-right">
                @if($claim->isPending())
                <div class="btn-toolbar">
                    <div class="btn-group dropdown">
                        <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                            <span class="dropdown-label">Actions</span>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right text-left text-sm">
                            <a data-toggle="modal" data-target="#modal_claim_accepted" class="dropdown-item btn-action--accepted btn-action-claim" data-action="accept" data-id="{{ $claim->id }}" href="#" >Mark as Accepted</a>
                            <a data-toggle="modal" data-target="#modal_claim_rejected" class="dropdown-item btn-action--rejected btn-action-claim" data-action="reject" data-id="{{ $claim->id }}" href="#">Mark as Rejected</a>
                        </div>
                    </div>
                </div>
                @elseif ($claim->wasAccepted())
                    <a class="btn white btn-sm">Shipping Label</a>
                @else

                @endif
                </div>
            </td>
        </tr>
    @endforeach
        </tbody>
    </table>


    @include('inventory.claims.partials._actionmodal')

@endsection