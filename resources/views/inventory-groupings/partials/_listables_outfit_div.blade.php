<h6>Outfit Pieces</h6>
@foreach($inventory as $item)
    @include('listables.partials._associated', [
        'listable' => $item,
        'route' => isset($listing) ? route('listingitems.show', $listing->items()->whereListableId($item->id)->first()->obfuscateIdToString()) : route('shop.inventory.show', [$item->user->username, $item->getUuid()])
    ])
@endforeach
