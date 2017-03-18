<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Presenters\Models\Listings\ListingsModelPresenter;

/**
 * ClASs Listings
 */
class Listings extends AbstractListingModel
{
    use ObfuscatesIdTrait, PresentableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'albums_count',
        'items_count',
        'accepted_sales_count',
        'pending_sales_count',
        'gross',
        'sale_name',
        'claimable_range',
        'type_icon_src'
    ];

    /**
     * @var string
     */
    protected $presenter = ListingsModelPresenter::class;

    /**
     * @var array
     */
    protected $with = [
//        'items'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'scheduled_for',
        'scheduled_until',
        'scheduled_for_deletion',
        'status_updated_at',
        'claimable_at',
        'claimable_until',
    ];

    /**
     * @var string
     */
    protected $table = 'listings';

    /**
     * @var array
     */
    protected $attributes = [
        'scheduled_for' => null,
        'scheduled_until' => null,
        'scheduled_for_deletion' => null,
        'claimable_until' => null,
        'claimable_at' => null,
        'owner_id' => 0,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'uuid' => '',
        'type' => self::TYPE_CUSTOM,
        'status' => self::STATUS_COMPLETED,
        'status_updated_at' => null,
        'status_history' => '',
        'name' => null,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'scheduled_for',
        'scheduled_until',
        'claimable_at',
        'claimable_until',
        'fb_group_node_id',
        'flashsale_id',
        'owner_id',
        'type',
        'status',
        'status_updated_at',
        'status_history',
        'name'
    ];

    /**
     * @return int
     */
    public function getAlbumsCountAttribute()
    {
        if($this->isFacebook()){
            return $this->listingItems()->groupBy('fb_album_node_id')->count();
        } else {
            return $this->listingItems()->groupBy('flashsale_id')->count();
        }
    }

    /**
     * @return int
     */
    public function getItemsCountAttribute()
    {
        return $this->listingItems->count();
    }

    public function getAcceptedSalesCountAttribute()
    {
        return $this->claims()->where('accepted', 1)->count();
    }

    /**
     * @return mixed
     */
    public function getPendingSalesCountAttribute()
    {
        return $this->pendingClaims()->count();
    }

    public function getGrossAttribute()
    {
        $entries = $this->claims()->where('accepted', 1)
            ->selectRaw(' IFNULL(SUM(CASE WHEN claims.accepted = 1 THEN (CASE WHEN claims.price IS NULL THEN claims.accepted_price ELSE claims.price END) ELSE 0 END),0) as gross')
            ->get();

        if($entries) {
            return $entries->sum('gross');
        }

        return 0;
    }

    /**
     * @return mixed
     */
    public function claims()
    {
        return $this->hasManyThrough(Claims::class, ListingItems::class, 'listing_id', 'listing_item_id')
            ->where('listable_type', ListingItems::class);
    }

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->claims()->where('accepted', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HASMany
     */
    public function listingItems()
    {
        return $this->items();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HASMany
     */
    public function items()
    {
        return $this->hasMany(ListingItems::class, 'listing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function listables()
    {
        return $this->belongsToMany(Listable::class, 'listing_items', 'listing_id', 'listable_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function facebookNode()
    {
        return $this->belongsTo(FacebookNodes::class, 'fb_group_node_id', 'facebook_node_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flashSale()
    {
        return $this->belongsTo(FlashSales::class, 'flashsale_id');
    }

    /**
     * With just 1 query, we can eASily make the necessary joins, SUMs, etc without n+1 issues.
     * This query returns the results for the listings table, bASed on the listings.index view needs.
     *
     * @param int $userId
     *
     * @return array
     */
    public static function getQueriedListings(int $userId)
    {
        $sql = "SELECT
                l.id as id,
                l.scheduled_for AS scheduled_for,
                l.status AS status,
                l.type as type,
                l.uuid AS uuid,
                s.name AS style_name,
                fs.name AS flashsale_name,
                l.name as custom_name,
                fb.facebook_node_name AS fb_name,
                li.fb_album_node_id as fb_album_id,
                li.fb_group_node_id as fb_group_id,
                li.flashsale_id as flashsale_id,
                IFNULL(COUNT(DISTINCT(li.id)), 0) AS items_count,
                IFNULL(COUNT(DISTINCT(li.fb_album_node_id)), 0) AS albums_count,
                IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
                IFNULL(SUM(c.accepted_price), 0) AS accepted_price_sum,
                IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
                IFNULL(SUM(c.accepted = 0),0) AS rejected_sales_count,
                IFNULL(SUM(v.count),0) AS pageviews_count,
                IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0) AS gross
                FROM listings AS l
                INNER JOIN listing_items AS li ON li.listing_id = l.id AND l.owner_id = li.owner_id AND l.type = li.type
                left JOIN listables AS i ON i.id = li.listable_id
                LEFT JOIN flashsales as fs ON fs.id = li.flashsale_id
                LEFT JOIN facebook_nodes AS fb ON fb.facebook_node_id = li.fb_group_node_id
                LEFT JOIN inventory_type_styles AS s ON s.id = i.inventory_type_styles_id
				LEFT JOIN claims AS c ON c.listing_item_id = li.id AND c.listable_id = li.listable_id AND c.claimed_by = l.owner_id
                LEFT JOIN v_pageviews as v on v.viewable_id = li.id AND v.viewable_type = 'Kabooodle\\\Models\\\ListingItems'
                WHERE l.owner_id = ? AND l.type = li.type AND l.id = li.listing_id
                AND l.deleted_at IS NULL
                AND l.deleted_at IS NULL 
                and li.deleted_at IS NULL
                GROUP BY l.id
                ORDER BY l.scheduled_for DESC
                ";

        return DB::select($sql, [$userId]);
    }

    /**
     * @return int
     */
    public function albumsCount()
    {
        return count($this->listingsGroupedByItemTypeGrouping());
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public function listingsGroupedByItemTypeGrouping($userId)
    {
        $sql = "
                SELECT
                i.subclass_name,
                l.id as id,
                fs.name AS flashsale_name,
                fb.facebook_node_name AS fb_name,
                li.fb_album_node_id as fb_album_id,
                li.fb_group_node_id as fb_group_id,
                li.flashsale_id as flashsale_id,
                l.name as custom_name,
                l.type as type,
                l.uuid as uuid,
                COUNT(DISTINCT(li.id)) AS items_count,
                IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
                IFNULL(SUM(c.accepted_price), 0) AS accepted_price_sum,
                IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
                IFNULL(SUM(c.accepted = 0),0) AS rejected_sales_count,
                IFNULL(SUM(v.count),0) AS pageviews_count,
                IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0) AS gross
                FROM listings AS l
                INNER JOIN listing_items AS li ON li.listing_id = l.id
                left JOIN listables AS i ON i.id = li.listable_id
                left JOIN inventory_type_styles AS s ON s.id = i.inventory_type_styles_id
                LEFT JOIN facebook_nodes AS fb ON fb.facebook_node_id = li.fb_album_node_id
                LEFT JOIN flashsales as fs ON fs.id = li.flashsale_id
				LEFT JOIN claims AS c ON c.listing_item_id = li.id AND c.listable_id = li.listable_id AND c.claimed_by = l.owner_id
                LEFT JOIN v_pageviews as v on v.viewable_id = li.id AND v.viewable_type = 'Kabooodle\\\Models\\\ListingItems'
                WHERE l.uuid = ? AND l.owner_id = ? AND l.type = li.type AND l.id = li.listing_id
                AND l.deleted_at IS NULL
                AND l.deleted_at IS NULL 
                and li.deleted_at IS NULL
                AND l.type = ?
                GROUP BY ::groupby::
                ORDER BY ::orderby:: ASC
                ";

        if ($this->isFacebook()) {
            $type = Listings::TYPE_FACEBOOK;
            $sql = str_replace('::groupby::', " li.fb_album_node_id ", $sql);
            $sql = str_replace('::orderby::', " fb.facebook_node_name ", $sql);
        } else {
            $type = Listings::TYPE_FLASHSALE;
            $sql = str_replace('::groupby::', " f.id ", $sql);
            $sql = str_replace('::orderby::', " fs.name ", $sql);
        }


        return DB::select($sql, [$this->uuid, $userId, $type]);
    }

    /**
     * @param $scope
     * @param string $operator
     * @param $date
     * @return mixed
     */
    public function scopeScheduledFor($scope, $operator = '>=', $date)
    {
        return $scope->where('scheduled_for', $operator, $date);
    }

    /**
     * @param $scope
     * @return $this
     */
    public function scopeStatusScheduled($scope)
    {
        return $scope->where('status', '=', self::STATUS_SCHEDULED);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function morphedType()
    {
        return $this->isFlashsale() ? $this->flashSale() : $this->facebookNode();
    }

    /**
     * @return mixed
     */
    public function getSaleNameAttribute()
    {
        if ($this->isCustomSale()) {
            return $this->name;
        }

        if ($this->isFacebook()) {
            return $this->facebookNode->facebook_node_name;
        }

        return $this->flashSale->name;
    }

    /**
     * @return string
     */
    public function getClaimableRangeAttribute()
    {
        $scheduledFor = $this->scheduled_for;
        $scheduledUntil = $this->scheduled_until;

        $claimableAt = $this->claimable_at ? : $scheduledFor;
        $claimableUntil = $this->claimable_until ? : ($scheduledUntil ? : null);

        return $claimableUntil ? $this->humanize($claimableAt).' - '.$this->humanize($claimableUntil) : $this->humanize($claimableAt);
    }

    /**
     * @return string
     */
    public function getTypeIconSrcAttribute()
    {
        return $this->typeIs(self::TYPE_FACEBOOK) ? '/assets/images/icons/FB-f-Logo__blue_18.png' : '/assets/images/icons/kabooodle_logo_18.png';
    }
    /**
     * @return mixed
     */
    public function claimableAfter()
    {
        if ($this->claimable_at) {
            return $this->claimable_at;
        }

        return $this->scheduled_for;
    }
}
