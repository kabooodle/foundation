<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Conner\Tagging\Taggable;

/**
 * Class TaggableTrait
 * @package Kabooodle\Models\Traits
 */
trait TaggableTrait
{
    use Taggable;

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsArray()
    {
        return $this->tagged()->pluck('tag_name')->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsList()
    {
        $a = $this->tagsArray();

        if ($a) {
            return implode(',', $a);
        }

        return null;
    }
}
