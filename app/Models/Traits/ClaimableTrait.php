<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

/**
 * Class ClaimableTrait
 * @package Kabooodle\Models\Traits
 */
trait ClaimableTrait
{

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->claims()->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function acceptedClaims()
    {
        return $this->claims()->where('accepted', true)->whereNotNull('accepted_on');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rejectedClaims()
    {
        return $this->claims()->where('accepted', false)->whereNotNull('rejected_on');
    }
}
