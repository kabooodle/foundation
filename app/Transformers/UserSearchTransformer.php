<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers;

use Kabooodle\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Class UserSearchTransformer
 */
class UserSearchTransformer extends TransformerAbstract
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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'hash' => $user->public_hash,
            'avatar' => $user->avatar,
            'username' => $user->username,
        ];
    }
}