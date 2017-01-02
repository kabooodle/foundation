<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
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
            'full_name' => $user->full_name,
            'username' => $user->username,
            'id' => $user->id
        ];
    }
}