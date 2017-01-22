<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\Listings;

/**
 * Class ListableTrait
 * @package Kabooodle\Models\Traits
 */
trait ListableTrait
{
    /**
     * @return string
     */
    public function getListingItemClass(): string
    {
        return $this->listingItemClass;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listings()
    {
        return $this->hasMany($this->getListingItemClass(), 'listed_id');
    }

    /**
     * @return mixed
     */
    public function flashsales()
    {
        return $this->listings()->where('type', Listings::TYPE_FLASHSALE);
    }

    /**
     * @return array|static[]
     */
    public function facebooksales()
    {
        return $this->listings()->where('type', Listings::TYPE_FACEBOOK);
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return number_format($this->price_usd, 2);
    }

    /**
     * @return string
     */
    public function getNameUuidAttribute() : string
    {
        return $this->getUUID();
    }

    /**
     * @return mixed
     */
    public function getCategoriesAttribute()
    {
        return $this->tags;
    }

    /**
     * @param int $qty
     *
     * @return bool
     */
    public function canSatisfyRequestedQuantityOf($qty = 1): bool
    {
        return $this->getAvailableQuantity() >= $qty;
    }
}
