<?php

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Listings
 */
class Listings extends AbstractListingModel
{
    use SoftDeletes, UuidableTrait;

    /**
     * @var array
     */
    protected $with = [
        'items'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'scheduled_for',
        'status_updated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var string
     */
    protected $table = 'listings';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'scheduled_for' => '',
        'owner_id' => 0,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'uuid' => '',
        'type' => self::TYPE_FACEBOOK,
        'status' => self::STATUS_SCHEDULED,
        'status_updated_at' => '',
        'status_history' => [],
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'scheduled_for',
        'fb_group_node_id',
        'flashsale_id',
        'owner_id',
        'type',
        'status',
        'status_updated_at',
        'status_history'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ListingItems::class, 'listing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function albums()
    {
        if($this->isFacebook()){
            return $this->items()->distinct('fb_album_node_id');
        }

        return null;
    }
}
