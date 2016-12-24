<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Models\Traits\WatchableTrait;
use Kabooodle\Models\Traits\ShoppableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Models\Contracts\WatchableInterface;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kabooodle\Presenters\Models\Listings\ListingItemsModelPresenter;

/**
 * Class ListingItems
 */
class ListingItems extends AbstractListingModel implements ShoppableInterface, WatchableInterface
{
    use ObfuscatesIdTrait, PresentableTrait, ShoppableTrait, SoftDeletes, UuidableTrait, WatchableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'name',
        'is_watched',
        'sale_name',
    ];

    /**
     * @var string
     */
    protected $presenter = ListingItemsModelPresenter::class;

    /**
     * @var array
     */
    protected $with = [
        'sales',
        'watchers'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'status_updated_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var string
     */
    protected $table = 'listing_items';

    /**
     * @var array
     */
    protected $casts = [
        'ignore' => 'bool'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'uuid' => '',
        'listing_id' => 0,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'fb_album_node_id' => null,
        'owner_id' => 0,
        'inventory_id' => 0,
        'type' => self::TYPE_FACEBOOK,
        'status' => self::STATUS_SCHEDULED,
        'status_updated_at' => '',
        'status_history' => '',
        'ignore' => false,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'listing_id',
        'fb_group_node_id',
        'flashsale_id',
        'fb_album_node_id',
        'owner_id',
        'inventory_id',
        'type',
        'status',
        'status_updated_at',
        'status_history',
        'ignore'
    ];

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        return $this->inventoryItem->name;
    }

    /**
     * @return mixed
     */
    public function claims()
    {
        return $this->morphMany(Claims::class, 'shoppable');
    }

    /**
     * @return mixed
     */
    public function deletedSales()
    {
        return $this->claims()->where('accepted', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flashsale()
    {
        return $this->belongsTo(FlashSales::class, 'flashsale_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listings::class, 'listing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pageViews()
    {
        return $this->morphMany(PageViews::class, 'shoppable');
    }

    /**
     * @return mixed
     */
    public function pendingSales()
    {
        return $this->claims()->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function rejectedSales()
    {
        return $this->deletedSales();
    }

    /**
     * @return mixed
     */
    public function sales()
    {
        return $this->claims()->where('accepted', 1);
    }

    /**
     * @return bool
     */
    public function isIgnored(): bool
    {
        return $this->ignore;
    }

    /**
     * @return string
     */
    public function getNameOfResource(): string
    {
        if($this->isFacebook()) {
            return 'Facebook Album';
        }

        return 'Flashsale';
    }

    /**
     * @return mixed
     */
    public function getSaleNameAttribute()
    {
        return $this->listing->sale_name;
    }

    /**
     * @return bool
     */
    public function includeLinkInDescr()
    {
        return $this->listing->includeLinkInDescr();
    }

    /**
     * @return bool
     */
    public function claimableBasedOnSchedule()
    {
        $now = Carbon::now();
        $claimableAt = $this->listing->claimable_at;
        $scheduledFor = $this->listing->scheduled_for;

        if ($scheduledFor) {
            if ($claimableAt) {
                if ($now >= $claimableAt && $now >= $scheduledFor) {
                    return true;
                }

                return false;
            }

            if ($now >= $scheduledFor) {
                return true;
            }
        }

        return false;
    }
}
