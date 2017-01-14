<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class PhoneNumber
 */
class PhoneNumber extends BaseEloquentModel
{
    use EloquentDatesTrait;

    /**
     * @var array
     */
    protected $dates = [
        'verified_on',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string
     */
    protected $table = 'phone_numbers';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'number',
        'verified',
        'verified_on',
        'token'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'verified' => 'bool',
        'verified_on' => 'date'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'phone_number' => 'required|digits:11',
            'code' => 'digits:4'
        ];
    }

    /**
     * @param $v
     *
     * @return mixed
     */
    public function getVerifiedOnAttribute($v)
    {
        if ($v) {
            $time = Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));

            return user() ? $time->tz(user()->timezone) : $time;
        }

        return $v;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return (bool) $this->verified;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
