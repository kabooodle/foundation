<?php

namespace Kabooodle\Models;

use DB;

/**
 * Class AbstractListingModel
 */
abstract class AbstractListingModel extends BaseEloquentModel
{
    const TYPE_FACEBOOK = 'facebook';
    const TYPE_FLASHSALE = 'flashsale';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_QUEUED_LIST = 'queued';
    const STATUS_PARTIAL = 'partial';
    const STATUS_SUCCESS = 'success';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELETED = 'deleted';
    const STATUS_QUEUED_DELETE = 'queued_delete';
    const STATUS_IGNORED_DUPLICATE = 'ignored_duplicate';

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
     * @param int $userId
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public static function queryGetItemsDuringDateTimeBlockForUser(int $userId, $startTime, $endTime)
    {
        $query = "SELECT * FROM listing_items as li 
        INNER JOIN listings as l ON l.id = li.listing_id
        WHERE (l.scheduled_for BETWEEN ? and ?)
        AND l.owner_id = ?";

        return DB::statement($query, [$startTime, $endTime, $userId]);
    }
}
