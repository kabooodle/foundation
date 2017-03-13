<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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

    /**
     * @return array|null
     */
    public function tagsString()
    {
        $a = $this->tagsArray();
        if ($a) {
            return implode(',', $a);
        }

        return null;
    }
}
