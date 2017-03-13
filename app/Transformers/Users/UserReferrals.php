<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Users;

use Kabooodle\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Class UserReferrals
 */
class UserReferrals extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $qualifiedReferral = $user->myAccountIsAQualifyingReferral() ? $user->qualifiedReferrals->where('referred_by_id', user()->id)->first() : null;

        return [
            'id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'already_following' => $user->is_followed ? true : false,
            'joined_on' => $user->created_at,
            'is_qualified' => $user->myAccountIsAQualifyingReferral(),
            'qualified_on' => $qualifiedReferral ? $qualifiedReferral->created_at : null,
            'follow_endpoint' => apiRoute('user.followers.store', [$user]),
        ];
    }
}
