<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Laravel\Cashier\Billable;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class SocialAccount
 * @package Kabooodle\Models
 */
class SocialAccount extends Authenticatable
{
    protected $fillable = ['user_id', 'provider_user_id', 'provider'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
