<?php

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Models\Traits\ShoppableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListingItems
 */
class ListingItems extends AbstractListingModel implements ShoppableInterface
{
    use ShoppableTrait, SoftDeletes, UuidableTrait;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listings::class, 'listing_id');
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
     * @return bool
     */
    public function isIgnored(): bool
    {
        return $this->ignore;
    }
}
