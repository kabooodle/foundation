<tr data-claim-id="{{ $claim->uuid }}" class=" @if($claim->wasRejected()) text-strikethrough strike text-muted @endif ">
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
        <span class="pending-status">@if($claim->wasRejected()) {{ $claim->rejected_on->diffForHumans() }} @elseif($claim->wasAccepted()) {{ $claim->accepted_on->diffForHumans() }} @else Pending  @endif</span>
    </td>
    <td style="vertical-align: middle !important" class="action-column">
        <div class="pull-right">
            @if($claim->isPending())
                <div class="btn-toolbar">
                    <div class="btn-group dropdown">
                        <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                            <span class="dropdown-label">Actions</span>
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right text-left text-sm">
                            <a data-toggle="modal" data-target="#modal_claim_accepted" class="dropdown-item btn-action--accepted btn-action-claim" v-on:click="toggleActionModal" data-action="accept" data-id="{{ $claim->uuid }}" data-method="put" data-route="{{ route('shop.claims.update', [$claim->inventoryItem->user->username, $claim->uuid]) }}" >Mark as Accepted</a>
                            <a data-toggle="modal" data-target="#modal_claim_rejected" class="dropdown-item btn-action--rejected btn-action-claim" v-on:click="toggleActionModal" data-action="reject" data-id="{{ $claim->uuid }}" data-method="delete" data-route="{{ route('shop.claims.destroy', [$claim->inventoryItem->user->username, $claim->uuid]) }}">Mark as Rejected</a>
                        </div>
                    </div>
                </div>
            @elseif ($claim->wasAccepted())
                <a href="{{ $claim->shipmentTransaction() ? route('shipping.labels.show', [$claim->shipmentTransaction()->shipping_shipments_uuid, $claim->shipmentTransaction()->uuid]) : route('shipping.create', ['c'=>$claim->uuid]) }}" class="btn white btn-sm">{{ $claim->shipmentTransaction() ? 'View Shipping Data' : 'Create Shipping Label' }}</a>
            @else

            @endif
        </div>
    </td>
</tr>