<tr class="  ">
    <td>
        <div class="avatar-thumbnail-container">
            <div class="avatar-thumbnail _32">
                @if($watch->watchable->listedItem->firstImage())
                    <img src="{{ $watch->watchable->listedItem->firstImage()->location }}">
                @else

                @endif
            </div>
        </div>
    </td>
    <td class="">{{ $watch->watchable->listedItem->name }}</td>
    <td>${{ $watch->watchable->listedItem->price_usd }}</td>
    <td>{{ $watch->watchable->listedItem->getOwner()->name }}</td>
    <td>{{ $watch->watchable->created_at->format('F jS Y g:i A') }}</td>
    <td>{{ $watch->watchable->updated_at->format('F jS Y g:i A') }}</td>
    <td>
        @if($watch->watchable->listedItem->canSatisfyRequestedQuantityOf(1))
            In stock
        @else
            Out of stock
        @endif
    </td>
    <td>
        <a class="btn white btn-xs btn-action" href="{{ route('listingitems.show', [$watch->watchable->obfuscateIdToString()]) }}">View</a>
        <form
                action="{{ apiRoute('listings.listingitems.watchers.destroy', [$watch->watchable->listing_id, $watch->watchable->id]) }}"
                method="post"
                class="inline inline-form"
                @submit="deleteWatching">
        {{ csrf_field() }}
        {{ method_field('delete') }}
        <button type="submit" class="btn white btn-xs btn-action btn-action-del">Remove</button>
        </form>
    </td>
</tr>
