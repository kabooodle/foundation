<tr data-claim-id="{{ $claim->uuid }}" class=" @if($claim->wasRejected()) text-strikethrough strike text-muted @endif ">
    <td style="vertical-align: middle !important">

    </td>
    <td style="vertical-align: middle !important">
        <a href="{{ route('shop.inventory.show', [webUser()->username, $claim->listable->obfuscateToURIStringFromModel()]) }}"
           class="_500 h6"><span class="@if($claim->wasRejected()) w-24 @else w-40 @endif avatar">
                                            <img src="{{ $claim->listable->cover_photo }}">
                                          </span></a>
    </td>
    <td style="vertical-align: middle !important">${{ $claim->listable_item_object_data['price_usd'] }}</td>
    <td style="vertical-align: middle !important">{!!  $claim->claimedBy->name  !!}</td>
    <td style="vertical-align: middle !important">{{ $claim->created_at->diffForHumans() }}</td>
    <td style="vertical-align: middle !important" class="action-column">
        <div class="pull-right">
            @if($claim->isPending())
                <a data-toggle="modal" data-target="#modal_claim_rejected" class="btn white btn-xs btn-action--rejected btn-action-claim" v-on:click="toggleActionModal" data-action="reject" data-id="{{ $claim->uuid }}" data-method="delete" data-route="{{ route('shop.claims.destroy', [$claim->listable->user->username, $claim->uuid]) }}">Reject</a>
                <a data-toggle="modal" data-target="#modal_claim_accepted" class="btn white btn-xs  btn-action--accepted btn-action-claim" v-on:click="toggleActionModal" data-action="accept" data-id="{{ $claim->uuid }}" data-method="put" data-route="{{ route('shop.claims.update', [$claim->listable->user->username, $claim->uuid]) }}" >Accept</a>
            @endif
        </div>
    </td>
</tr>