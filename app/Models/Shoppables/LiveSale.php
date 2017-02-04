<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Shoppables;

use Kabooodle\Models\Inventory;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LiveSale
 */
class LiveSale extends AbstractShoppable implements ShoppableInterface
{
    /**
     * @var string
     */
    protected $table = 'shop_livesale';

    /**
     * @return string
     */
    public function getNameOfShoppable(): string
    {
        return 'Live Sale';
    }

    /**
     * @return BelongsTo
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
