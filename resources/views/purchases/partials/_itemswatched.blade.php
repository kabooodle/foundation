<tr class="  ">
    <td>
        <div class="avatar-thumbnail-container">
            <div class="avatar-thumbnail _32">
                @if($watch->watchable->inventoryItem->firstImage())
                <a href="{{ route('listingitems.show', [$watch->watchable->obfuscateIdToString()]) }}">
                    <img src="{{ $watch->watchable->inventoryItem->firstImage()->location }}">
                </a>
                    @else

                @endif
            </div>
        </div>
    </td>
    <td class="">{{ $watch->watchable->inventoryItem->name }}</td>
    <td>${{ $watch->watchable->inventoryItem->price_usd }}</td>
    <td>{{ $watch->watchable->inventoryItem->getOwner()->name }}</td>
    <td>{{ $watch->watchable->created_at->format('F jS Y g:i A') }}</td>
    <td>
        {{ Form::open(array('route' => array('watching.items.destroy', user()->username, $watch->id), 'method' => 'delete')) }}
        <button type="submit" class="btn btn-danger btn-mini">Delete</button>
        {{ Form::close() }}
    </td>
</tr>
