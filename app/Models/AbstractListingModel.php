<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;

/**
 * Class AbstractListingModel
 */
abstract class AbstractListingModel extends BaseEloquentModel
{
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_FLASHSALE = 'flashsale';
    const TYPE_CUSTOM = 'custom';
    const TYPES = [
        self::TYPE_FACEBOOK,
        self::TYPE_FLASHSALE,
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SCHEDULED_DELETE = 'scheduled_delete';
    const STATUS_QUEUED_LIST = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSING_DELETE = 'processing_delete';
    const STATUS_PARTIAL = 'partial';
    const STATUS_SUCCESS = 'success';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELETED = 'deleted';
    const STATUS_QUEUED_DELETE = 'queued_delete';
    const STATUS_IGNORED_DUPLICATE = 'ignored_duplicate';
    const STATUS_FAILED = 'failed';
    const STATUS_FAILED_DELETE = 'delete_failed';
    const STATUS_THROTTLED = 'throttled';
    const STATUSES = [
        self::STATUS_SCHEDULED,
        self::STATUS_SCHEDULED_DELETE,
        self::STATUS_PROCESSING_DELETE,
        self::STATUS_QUEUED_LIST,
        self::STATUS_PROCESSING,
        self::STATUS_PARTIAL,
        self::STATUS_SUCCESS,
        self::STATUS_COMPLETED,
        self::STATUS_DELETED,
        self::STATUS_QUEUED_DELETE,
        self::STATUS_IGNORED_DUPLICATE,
        self::STATUS_FAILED,
        self::STATUS_THROTTLED,
        self::STATUS_FAILED_DELETE
    ];

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
     * @return mixed
     */
    public function scopeFacebook($scope)
    {
        return $scope->where('type', self::TYPE_FACEBOOK);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function scopeFlashsale($scope)
    {
        return $scope->where('type', self::TYPE_FLASHSALE);
    }

    /**
     * @param $scope
     * @return mixed
     */
    public function scopeCustom($scope)
    {
        return $scope->where('type', self::TYPE_CUSTOM);
    }

    /**
     * @param $value
     */
    public function setStatusHistoryAttribute($value)
    {
        $this->attributes['status_history'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getStatusHistoryAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->statisIs(self::STATUS_DELETED);
    }

    /**
     * @return bool
     */
    public function isQueuedToList()
    {
        return $this->statisIs(self::STATUS_QUEUED_LIST);
    }

    /**
     * @return bool
     */
    public function isQueuedToDelete()
    {
        return $this->statisIs(self::STATUS_QUEUED_DELETE);
    }

    /**
     * @return bool
     */
    public function isFacebook()
    {
        return $this->typeIs(self::TYPE_FACEBOOK);
    }

    /**
     * @return bool
     */
    public function isFlashsale()
    {
        return $this->typeIs(self::TYPE_FLASHSALE);
    }

    /**
     * @return bool
     */
    public function isCustomSale()
    {
        return $this->typeIs(self::TYPE_CUSTOM);
    }

    /**
     * @param $type
     * @return bool
     */
    public function typeIs($type)
    {
        return $this->type == $type;
    }

    /**
     * @param $status
     * @return bool
     */
    public function statisIs($status)
    {
        return $this->status == $status;
    }

    /**
     * @param $status
     *
     * @return bool
     */
    public static function isStillEditable($status)
    {
        return ! in_array($status, [
            static::STATUS_QUEUED_LIST,
            static::STATUS_COMPLETED,
            static::STATUS_SUCCESS,
            static::STATUS_QUEUED_DELETE,
            static::STATUS_DELETED
        ]);
    }

    /**
     * @param int $userId
     * @param $startTime
     * @return bool
     */
    public static function queryGetItemsDuringDateTimeBlockForUser(int $userId, $startTime)
    {
        $query = " 
        SELECT * FROM listing_items as li 
        INNER JOIN listings as l ON l.id = li.listing_id
        WHERE l.scheduled_for BETWEEN date_sub(?, interval 1 hour) and date_add(?, interval 1 hour)
        AND l.owner_id = ?
        AND li.ignore = 0
        AND li.deleted_at is null
        AND l.deleted_at is null
        
        UNION
        
        SELECT * FROM listing_items as li 
        INNER JOIN listings as l ON l.id = li.listing_id
        WHERE l.scheduled_for_deletion BETWEEN date_sub(?, interval 1 hour) and date_add(?, interval 1 hour)
        AND l.owner_id = ?
        AND li.ignore = 0
        AND li.deleted_at is null
        AND l.deleted_at is null
        ";

        return DB::select($query, [$startTime, $startTime, $userId, $startTime, $startTime, $userId]);
    }

    /**
     * @param int $userId
     * @param     $startTime
     * @param     $endTime
     * @param int $incomingItemsCount
     *
     * @return bool
     */
    public static function checkIfAttemptedListingExceedsHourlyQuota(int $userId, $startTime, $endTime, int $incomingItemsCount)
    {
        $results = self::queryGetItemsDuringDateTimeBlockForUser($userId, $startTime, $endTime);
        $countResults = count($results);

        return ($countResults + $incomingItemsCount) > 900;
    }

    /**
     * @param Carbon $startTime
     * @param Carbon $endTime
     *
     * @return mixed
     */
    public static function getScheduledListings(Carbon $startTime, Carbon $endTime)
    {
        return Listings::noEagerLoads()
            ->facebook()
            ->scheduledFor('>=', $startTime->format('Y-m-d H:i:s'))
            ->scheduledFor('<=', $endTime->format('Y-m-d H:i:s'))
            ->statusScheduled()
            ->randomize()
            ->get();
    }

    /**
     * @param Carbon $startTime
     * @param Carbon $endTime
     *
     * @return mixed
     */
    public static function getScheduledForDeletionListings(Carbon $startTime, Carbon $endTime)
    {
        return Listings::noEagerLoads()
            ->facebook()
            ->where('scheduled_for_deletion', '>=', $startTime->format('Y-m-d H:i:s'))
            ->where('scheduled_for_deletion', '<=', $endTime->format('Y-m-d H:i:s'))
            ->where('status', '=', self::STATUS_SCHEDULED_DELETE)
            ->randomize()
            ->get();
    }

    /**
     * @param Carbon $startTime
     * @param Carbon $endTime
     *
     * @return mixed
     */
    public static function getScheduledForDeletionListingItems(Carbon $startTime, Carbon $endTime)
    {
        return ListingItems::noEagerLoads()
            ->facebook()
            ->where('scheduled_for_deletion', '>=', $startTime->format('Y-m-d H:i:s'))
            ->where('scheduled_for_deletion', '<=', $endTime->format('Y-m-d H:i:s'))
            ->where('status', '=', self::STATUS_SCHEDULED_DELETE)
            ->randomize()
            ->get();
    }

    /**
     * @param array $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @return bool|int
     */
    public static function updateListingsStatus(array $listingIds, Carbon $timestamp, string $status = Listings::STATUS_QUEUED_LIST)
    {
        return Listings::whereIn('id', $listingIds)
            ->noEagerLoads()
            ->update([
                'status' => $status,
                'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
            ]);
    }

    /**
     * @param array  $listingIds
     * @param Carbon $timestamp
     * @param string $status
     * @param array  $additionalAttributes
     *
     * @return mixed
     */
    public static function updateListingItemsStatus(array $listingIds, Carbon $timestamp, string $status = Listings::STATUS_QUEUED_LIST, array $additionalAttributes = [])
    {
        $attributes = [
            'status' => $status,
            'status_updated_at' => $timestamp->format('Y-m-d H:i:s')
        ];

        if ($additionalAttributes && count($additionalAttributes) > 0) {
            $attributes = array_merge($attributes, $additionalAttributes);
        }

        return ListingItems::whereIn('id', $listingIds)
            ->noEagerLoads()
            ->update($attributes);
    }

    /**
     * @param string $listingUuid
     *
     * @return mixed
     */
    public static function getStyleGroupings(string $listingUuid)
    {
        return DB::table('listables')
            ->leftJoin('inventory_type_styles', 'inventory_type_styles.id', '=', 'listables.inventory_type_styles_id')
            ->leftJoin('inventory_sizes', 'inventory_sizes.id', '=', 'listables.inventory_sizes_id')
            ->join('listing_items', 'listing_items.listable_id', '=', 'listables.id')
            ->join('listings','listings.id', '=', 'listing_items.listing_id')
            ->where('listings.uuid', $listingUuid)
            ->whereNull('listing_items.deleted_at')
            ->whereNull('listings.deleted_at')
            ->whereNull('listables.deleted_at')
            ->groupBy('listables.uuid')
            ->groupBy('inventory_type_styles_id')
            ->groupBy('inventory_sizes_id')
            ->selectRaw("
                inventory_sizes.id as size_id, 
                IFNULL(inventory_type_styles.id, 'outfits') as style_id,
                IFNULL(inventory_type_styles.name, 'Outfits') as style_name,
                inventory_sizes.name as size_name
            ")
            ->get();
    }

    /**
     * @param int $flashsaleId
     *
     * @return array|static[]
     */
    public static function getStyleGroupingsForFlashsale(int $flashsaleId)
    {
        return DB::table('listables')
            ->leftJoin('inventory_type_styles', 'inventory_type_styles.id', '=', 'listables.inventory_type_styles_id')
            ->leftJoin('inventory_sizes', 'inventory_sizes.id', '=', 'listables.inventory_sizes_id')
            ->join('listing_items', 'listing_items.listable_id', '=', 'listables.id')
            ->join('listings','listings.id', '=', 'listing_items.listing_id')
            ->where('listing_items.flashsale_id', $flashsaleId)
            ->whereNull('listing_items.deleted_at')
            ->whereNull('listings.deleted_at')
            ->whereNull('listables.deleted_at')
            ->groupBy('listables.uuid')
            ->groupBy('inventory_type_styles_id')
            ->groupBy('inventory_sizes_id')
            ->selectRaw("
                inventory_sizes.id as size_id, 
                IFNULL(inventory_type_styles.id, 'outfits') as style_id,
                IFNULL(inventory_type_styles.name, 'Outfits') as style_name,
                inventory_sizes.name as size_name
            ")
            ->get();
    }

    /**
     * @param int $flashsaleId
     *
     * @return array|static[]
     */
    public static function getSellersForFlashsale(int $flashsaleId)
    {
        return DB::table('users')
            ->join('listing_items', 'listing_items.owner_id', '=', 'users.id')
            ->join('listings','listings.id', '=', 'listing_items.listing_id')
            ->where('listing_items.flashsale_id', $flashsaleId)
            ->whereNull('listing_items.deleted_at')
            ->whereNull('listings.deleted_at')
            ->groupBy('users.id')
            ->select([
                'users.id as id',
                'users.username as username',
                'users.first_name as firstname',
                'users.last_name as lastname',
                DB::raw('count(listing_items.id) AS items_count'),
            ])
            ->get();
    }
}
