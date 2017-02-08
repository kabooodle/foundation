<?php

namespace Kabooodle\Tests\Unit;

use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\ListingItemGrouping;
use Kabooodle\Models\ListingItemSingle;
use Kabooodle\Models\Listings;
use Kabooodle\Tests\BaseTestCase;

class ListingsTest extends BaseTestCase
{
    public function testListingItemsReturnsCollectionOfSubclasses()
    {
        $listing = factory(Listings::class)->create([
            'flashsale_id' => null,
        ]);
        $item1 = factory(ListingItemGrouping::class)->create([
            'listing_id' => $listing->id,
            'owner_id' => $listing->owner_id,
            'flashsale_id' => $listing->flashsale_id,
        ]);
        $item2 = factory(ListingItemSingle::class)->create([
            'listing_id' => $listing->id,
            'owner_id' => $listing->owner_id,
            'flashsale_id' => $listing->flashsale_id,
        ]);

        $listing = Listings::with('items.listedItem')->find($listing->id);
        $listing->loadItemsListedItem();

        $this->assertEquals($item1->listedItem->id, $listing->items->first()->listedItem->id, '1');
        $this->assertEquals($item2->listedItem->id, $listing->items->last()->listedItem->id, '2');
    }
}