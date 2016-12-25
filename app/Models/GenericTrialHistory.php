<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class GenericTrialHistory
 */
class GenericTrialHistory extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'users_trialed_subscription';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'trial_ends_at'
    ];
}