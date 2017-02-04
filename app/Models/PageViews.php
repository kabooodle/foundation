<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class PageViews
 */
class PageViews extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'pageviews';

    /**
     * @var array
     */
    protected $fillable = [
        'shoppable_type',
        'shoppable_id',
        'inventory_id',
        'ip_address'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shoppable()
    {
        return $this->morphTo('shoppable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryItem()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
