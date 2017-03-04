<h6>Outfits</h6>
@foreach($groupings as $grouping)
    @include('listables.partials._associated', [
        'listable' => $grouping,
        'route' => isset($listing) ? route('listingitems.show', $listing->items()->whereListableId($grouping->id)->first()->obfuscateIdToString()) : route('shop.outfits.show', [$grouping->user->username, $grouping->getUuid()])
    ])
@endforeach
