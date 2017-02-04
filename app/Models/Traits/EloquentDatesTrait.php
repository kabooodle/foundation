<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Carbon\Carbon;

/**
 * Class EloquentDatesTrait
 */
trait EloquentDatesTrait
{
    /**************************************************************************/
    /*************** USED TO CONVERT TIMEZONES TO USER TIMEZONES **************/
    /**************************************************************************/
    /**
     * @param $value
     * @return bool|null|string
     */
    public static function formatDateAttribute($value)
    {
        if (! $value or is_null($value)) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->setTimezone(current_timezone());
    }

    /**
     * @param $value
     *
     * @return Carbon
     */
    public static function convertToUTC($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, current_timezone())->setTimezone('UTC');
    }

    /**
     * Overload Laravel setAttribute to convert dates to UTC.
     *
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        if ($value && (in_array($key, $this->getDates()) || $this->isDateCastable($key))) {
            if (!isset($value->tzName) || $value->tzName <> 'UTC') {
                $value = static::convertToUTC($this->fromDateTime__set($value));
            }

            $this->attributes[$key] = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param $key
     *
     * @return bool|null|string
     */
    public function getAttributeValue($key)
    {
//        Disabled by JT Jan 10, 2017, dont think we need this now.
//        $value = $this->getAttributeFromArray($key);
//
//        if (in_array($key, $this->getDates()) && ! is_null($value)) {
//            return static::formatDateAttribute(parent::asDateTime($value));
//        }

        return parent::getAttributeValue($key);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        $value = parent::asDateTime($value);

        return $value->setTimezone(current_timezone());
    }

    /**
     * {@inheritdoc}
     */
    public function fromDateTime__set($value)
    {
        $format = $this->getDateFormat();

        // Call parent method because local method converts to user TZ
        $value = parent::asDateTime($value);

        return $value->format($format);
    }
    /**************************************************************************/
    /**************************** END TIMEZONE CODE ***************************/
    /**************************************************************************/


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
