<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Presenters\Models\Listings\ListingsModelPresenter;

/**
 * ClASs Listings
 */
class Listings extends AbstractListingModel
{
    use PresentableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'albums_count',
        'items_count',
        'use_link',
        'accepted_sales_count',
        'pending_sales_count',
        'gross',
        'sale_name'
    ];

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
    protected $casts = [
        'include_link_in_descr' => 'bool'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'include_link_in_descr' => true,
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
        'include_link_in_descr',
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

    /**
     * @return bool
     */
    public function getUseLinkAttribute()
    {
        return $this->includeLinkInDescr();
    }

    public function getAcceptedSalesCountAttribute()
    {
        return $this->claims()->where('accepted', 1)->count();
    }

    public function getPendingSalesCountAttribute()
    {
        return $this->claims()->where('accepted', null)->count();
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
        return $this->hasManyThrough(Claims::class, ListingItems::class, 'listing_id', 'shoppable_id')
            ->where('shoppable_type', ListingItems::class);
    }

    /**
     * @return \Illuminate\DatabASe\Eloquent\Relations\HASMany
     */
    public function listingItems()
    {
        return $this->items();
    }

    /**
     * @return \Illuminate\DatabASe\Eloquent\Relations\HASMany
     */
    public function items()
    {
        return $this->hasMany(ListingItems::class, 'listing_id');
    }

    /**
     * @return \Illuminate\DatabASe\Eloquent\Relations\BelongsTo
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
        //                IFNULL(COUNT(DISTINCT(p.id)), 0) AS pageviews_count,
        //                LEFT JOIN pageviews AS p ON p.shoppable_id = li.id AND p.inventory_id = li.inventory_id
        $sql = "SELECT
                l.scheduled_for AS scheduled_for,
                l.include_link_in_descr as use_link,
                l.status AS status,
                l.type as type,
                l.uuid AS uuid,
                s.name AS style_name,
                fs.name AS flashsale_name,
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
                IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0) AS gross
                FROM listings AS l
                INNER JOIN listing_items AS li ON li.listing_id = l.id AND l.owner_id = li.owner_id AND l.type = li.type
                INNER JOIN inventory AS i ON i.id = li.inventory_id
                LEFT JOIN flashsales as fs ON fs.id = li.flashsale_id
                LEFT JOIN facebook_nodes AS fb ON fb.facebook_node_id = li.fb_group_node_id
                LEFT JOIN inventory_type_styles AS s ON s.id = i.inventory_type_styles_id
				LEFT JOIN claims AS c ON c.shoppable_id = li.id AND c.inventory_id = li.inventory_id AND c.claimed_by = l.owner_id
                WHERE l.owner_id = ? AND l.type = li.type AND l.id = li.listing_id
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
                fs.name AS flashsale_name,
                fb.facebook_node_name AS fb_name,
                li.fb_album_node_id as fb_album_id,
                li.fb_group_node_id as fb_group_id,
                li.flashsale_id as flashsale_id,
                l.type as type,
                l.uuid as uuid,
                COUNT(DISTINCT(li.id)) AS items_count,
                IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
                IFNULL(SUM(c.accepted_price), 0) AS accepted_price_sum,
                IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
                IFNULL(SUM(c.accepted = 0),0) AS rejected_sales_count,
                IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0) AS gross
                FROM listings AS l
                INNER JOIN listing_items AS li ON li.listing_id = l.id
                INNER JOIN inventory AS i ON i.id = li.inventory_id
                INNER JOIN inventory_type_styles AS s ON s.id = i.inventory_type_styles_id
                LEFT JOIN facebook_nodes AS fb ON fb.facebook_node_id = li.fb_album_node_id
                LEFT JOIN flashsales as fs ON fs.id = li.flashsale_id
				LEFT JOIN claims AS c ON c.shoppable_id = li.id AND c.inventory_id = li.inventory_id AND c.claimed_by = l.owner_id
                WHERE l.uuid = ? AND l.owner_id = ? AND l.type = li.type AND l.id = li.listing_id
                AND l.type = ?
                GROUP BY ::groupby::
                ORDER BY l.scheduled_for DESC
                ";

        if ($this->isFacebook()) {
            $type = Listings::TYPE_FACEBOOK;
            $sql = str_replace('::groupby::', " li.fb_album_node_id ", $sql);
        } else {
            $type = Listings::TYPE_FLASHSALE;
            $sql = str_replace('::groupby::', "s.id ", $sql);
        }

        return DB::select($sql, [$this->uuid, $userId, $type]);
    }

    /**
     * @return bool
     */
    public function includeLinkInDescr()
    {
        return $this->include_link_in_descr;
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
    public function scopeRandomize($scope)
    {
        return $scope->orderByRaw('RAND()');
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
     * @return mixed
     */
    public function getSaleNameAttribute()
    {
        if ($this->isFacebook()) {
            return $this->facebookNode->facebook_node_name;
        }

        return $this->flashSale->name;
    }
}
