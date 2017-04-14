<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Tests\Integration;

use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;

class InventoryGroupingsTest extends BaseIntegrationTestCase
{
    /**
     * Test that inventory in locked grouping can not satisfy request of amount
     * locked by grouping
     */
    public function testItemsOfLockedGroupingCanNotBeClaimedIndividually()
    {
        $user = factory(User::class)->create();
        $inventory1 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
        ]);
        $inventory2 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 2,
        ]);

        $grouping = factory(InventoryGrouping::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
            'locked' => true,
        ]);
        $grouping->inventoryItems()->sync([$inventory1->id, $inventory2->id]);

        $this->assertFalse($inventory1->canSatisfyRequestedQuantityOf(1));
        $this->assertFalse($inventory2->canSatisfyRequestedQuantityOf(2));
        $this->assertTrue($inventory2->canSatisfyRequestedQuantityOf(1));
    }

    /**
     * Test that inventory in unlocked grouping can satisfy request of any amount
     * up to amount on hand
     */
    public function testItemsOfUnlockedGroupingCanBeClaimedIndividually()
    {
        $user = factory(User::class)->create();
        $inventory1 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
        ]);
        $inventory2 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 2,
        ]);

        $grouping = factory(InventoryGrouping::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
            'locked' => false,
        ]);
        $grouping->inventoryItems()->sync([$inventory1->id, $inventory2->id]);

        $this->assertTrue($inventory1->canSatisfyRequestedQuantityOf(1));
        $this->assertTrue($inventory2->canSatisfyRequestedQuantityOf(2));
    }

    /**
     * Test that decrementing an inventory grouping decrements its items
     */
    public function testDecrementOnInventoryGroupingDecrementsItems()
    {
        $user = factory(User::class)->create();
        $inventory1 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
        ]);
        $inventory2 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 2,
        ]);

        $grouping = factory(InventoryGrouping::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
            'locked' => false,
        ]);
        $grouping->inventoryItems()->sync([$inventory1->id, $inventory2->id]);

        $grouping->decrementInitialQty(1);

        $this->assertEquals(0, $grouping->initial_qty);
        $this->assertEquals(0, $grouping->inventoryItems->find($inventory1->id)->initial_qty);
        $this->assertEquals(1, $grouping->inventoryItems->find($inventory2->id)->initial_qty);
    }

    /**
     * Test that incrementing an inventory grouping increments its items
     */
    public function testIncrementOnInventoryGroupingIncrementsItems()
    {
        $user = factory(User::class)->create();
        $inventory1 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
        ]);
        $inventory2 = factory(Inventory::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 2,
        ]);

        $grouping = factory(InventoryGrouping::class)->create([
            'user_id' => $user->id,
            'initial_qty' => 1,
            'locked' => false,
        ]);
        $grouping->inventoryItems()->sync([$inventory1->id, $inventory2->id]);

        $grouping->incrementInitialQty(1);

        $this->assertEquals(2, $grouping->initial_qty);
        $this->assertEquals(2, $grouping->inventoryItems->find($inventory1->id)->initial_qty);
        $this->assertEquals(3, $grouping->inventoryItems->find($inventory2->id)->initial_qty);
    }
}