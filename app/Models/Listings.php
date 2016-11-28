<?php

namespace Kabooodle\Models;

use DB;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Presenters\Models\Listings\ListingsModelPresenter;

/**
 * Class Listings
 */
class Listings extends AbstractListingModel
{
    use PresentableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var string
     */
    protected $presenter = ListingsModelPresenter::class;

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
        'scheduled_for' => '',
        'owner_id' => 0,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'uuid' => '',
        'type' => self::TYPE_FACEBOOK,
        'status' => self::STATUS_SCHEDULED,
        'status_updated_at' => null,
        'status_history' => '',
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
    public function listingItems()
    {
        return $this->items();
    }

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


    public function sales()
    {
        //
    }

    /**
     * @return int
     */
    public function albumsCount()
    {
        return count($this->listingsGroupedByItemTypeGrouping());
    }

    /**
     * @return int
     */
    public function listingsGroupedByItemTypeGrouping()
    {
        // Group by FB Albums
        if ($this->isFacebook()) {
            $sql = "
                SELECT
                li.fb_album_node_id as name,
                li.`fb_album_node_id`,
                li.fb_group_node_id,
                count(li.id) as items_count,
                sum(c.accepted = null) as pending_sales_count,
                sum(c.accepted = 1) as accepted_sales_count,
                sum(c.accepted = 0) as rejected_sales_count,
                sum(c.price) as price_sum,
                sum(c.accepted_price) as accepted_price_sum
                FROM listings as l
                INNER JOIN listing_items as li ON li.listing_id = l.id
                INNER JOIN inventory as i ON i.id = li.inventory_id
                LEFT JOIN claims as c ON c.`inventory_id` = i.id
                WHERE l.uuid = ?
                AND l.type = 'facebook'
                GROUP BY li.fb_album_node_id
             ";
        } else {
            $sql = "
                SELECT
                s.name as name,
                li.flashsale_id,
                count(li.id) as items_count,
                sum(c.accepted = null) as pending_sales_count,
                sum(c.accepted = 1) as accepted_sales_count,
                sum(c.accepted = 0) as rejected_sales_count,
                sum(c.price) as price_sum,
                sum(c.accepted_price) as accepted_price_sum
                FROM listings as l
                INNER JOIN listing_items as li ON li.listing_id = l.id
                INNER JOIN inventory as i ON i.id = li.inventory_id
                INNER JOIN inventory_type_styles as s ON s.id = i.inventory_type_styles_id
                LEFT JOIN claims as c ON c.`inventory_id` = i.id
                WHERE l.uuid = ?
                AND l.type = 'flashsale'
                GROUP BY s.id
                ";
        }

        return DB::select($sql, [$this->uuid]);
    }
}
