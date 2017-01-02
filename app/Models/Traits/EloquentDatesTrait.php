<?php

namespace Kabooodle\Models\Traits;

use Carbon\Carbon;

/**
 * Class EloquentDatesTrait
 */
trait EloquentDatesTrait
{
    /**
     * @param $v
     *
     * @return string
     */
    public function getCreatedAtAttribute($v)
    {
        $time = Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));

        return user() ? $time->tz(user()->timezone) : $time;
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function getUpdatedAtAttribute($v)
    {
        $time = Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));

        return user() ? $time->tz(user()->timezone) : $time;
    }

    /**
     * @param $date
     *
     * @return string
     */
    public function convertDateTimeTo8601($date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(DATE_ISO8601);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeNoAppends($query)
    {
        return $query->setAppends([]);
    }

    /**
     * @param string $format
     *
     * @return null
     */
    public function createdAtHuman($format = 'm-d-Y h:ia')
    {
        if ($this->created_at) {
            return $this->created_at->format($format);
        }

        return null;
    }

    /**
     * @return null
     */
    public function createdAtHumanNoTime()
    {
        return $this->createdAtHuman('m-d-Y');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function humanize($value)
    {
        return humanizeDateTime($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function humanizeNoTime($value)
    {
        return humanizeDate($value);
    }

    /**
     * @return null|string
     */
    public function updatedAtHuman()
    {
        if ($this->updated_at) {
            return $this->updated_at->format('m-d-Y h:ia');
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->createdAtHuman();
    }

    /**
     * @return null|string
     */
    public function getUpdatedAtHumanAttribute()
    {
        return $this->updatedAtHuman();
    }
}
