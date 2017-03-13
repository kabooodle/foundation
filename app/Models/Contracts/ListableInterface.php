<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

/**
 * Interface ListableInterface
 * @package Kabooodle\Models\Contracts
 */
interface ListableInterface
{
    /**
     * @return string
     */
    public function getEditRoute(): string;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItems();

    /**
     * @return mixed
     */
    public function flashsales();

    /**
     * @return mixed
     */
    public function facebooksales();

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getTitleAttribute(): string;

    /**
     * @return string
     */
    public function getPrice(): string;

    /**
     * @param int $qty
     *
     * @return bool
     */
    public function canSatisfyRequestedQuantityOf($qty = 1): bool;

    /**
     * @return int
     */
    public function getAvailableQuantity(): int;

    /**
     * @return int
     */
    public function getOnHoldQuantity(): int;

    /**
     * @return mixed
     */
    public function getCategoriesAttribute();

    /**
     * @param $value
     * @return float
     */
    public function getWholesalePriceUsdAttribute($value): float;

    /**
     * @param int $amount
     * @return mixed
     */
    public function decrementInitialQty(int $amount = 1);

    /**
     * @param int $amount
     * @return mixed
     */
    public function incrementInitialQty(int $amount = 1);

    /**
     * @return mixed
     */
    public function claims();

    /**
     * @return mixed
     */
    public function pendingClaims();

    /**
     * @return mixed
     */
    public function acceptedClaims();

    /**
     * @return mixed
     */
    public function rejectedClaims();

    /**
     * @return string
     */
    public function getListableTypeFriendlyNameAttribute(): string;
}
