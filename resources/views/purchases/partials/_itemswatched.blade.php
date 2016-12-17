<tr class="  ">
    <td>
        <div class="avatar-thumbnail-container">
            <div class="avatar-thumbnail _32">
                @if($watch->watchable->inventoryItem->firstImage())
                    <img src="{{ $watch->watchable->inventoryItem->firstImage()->location }}">
                @else

                @endif
            </div>
        </div>
    </td>
    <td class="">{{ $watch->watchable->inventoryItem->name }}</td>
    <td>${{ $watch->watchable->inventoryItem->price_usd }}</td>
    <td>{{ $watch->watchable->inventoryItem->getOwner()->name }}</td>
    <td>{{ $watch->watchable->created_at->format('F jS Y g:i A') }}</td>
    <td>{{ $watch->watchable->updated_at->format('F jS Y g:i A') }}</td>
    <td>
        @if($watch->watchable->inventoryItem->canSatisfyRequestedQuantityOf(1))
            In stock
        @else
            Out of stock
        @endif
    </td>
    <td>
        <a class="btn white btn-xs btn-action" href="{{ route('listingitems.show', [$watch->watchable->obfuscateIdToString()]) }}">View</a>
    </td>
    <td>
        {{ Form::open(array('route' => array('watching.items.destroy', user()->username, $watch->id), 'method' => 'delete')) }}
        <button type="submit" class="btn white btn-xs btn-action">Remove</button>
        {{ Form::close() }}
    </td>
</tr>
