<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Follows;

use Kabooodle\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Class FollowersTransformer
 */
class FollowersTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'already_following' => $user->is_followed ? true : false,
            'follow_endpoint' => apiRoute('user.followers.store', [$user]),
        ];
    }
}
