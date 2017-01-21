<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        'viewable_type',
        'viewable_id',
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function listedItem()
    {
        return $this->morphTo('listable');
    }
}
