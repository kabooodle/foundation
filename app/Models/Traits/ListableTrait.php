<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\ListingItems;
use Kabooodle\Models\Listings;

/**
 * Class ListableTrait
 * @package Kabooodle\Models\Traits
 */
trait ListableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItems()
    {
        return $this->hasMany(ListingItems::class, 'listable_id');
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

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->claims()->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function acceptedClaims()
    {
        return $this->claims()->where('accepted', true)->whereNotNull('accepted_on');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rejectedClaims()
    {
        return $this->claims()->where('accepted', false)->whereNotNull('rejected_on');
    }
}
