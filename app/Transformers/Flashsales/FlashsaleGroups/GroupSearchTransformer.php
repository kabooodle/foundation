<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Flashsales\FlashsaleGroups;

use Kabooodle\Models\FlashsaleGroups;
use League\Fractal\TransformerAbstract;

/**
 * Class GroupSearchTransformer
 */
class GroupSearchTransformer extends TransformerAbstract
{
    /**
     * @param FlashsaleGroups $group
     *
     * @return array
     */
    public function transform(FlashsaleGroups $group)
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'users' => $group->users->pluck('id')
        ];
    }
}