<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Illuminate\Cache\Repository;
use Kabooodle\Models\View;

/**
 * Class ViewableTrait
 * @package Kabooodle\Models\Traits
 */
trait ViewableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * @return mixed
     */
    public function totalViews()
    {
        return $this->views()->count();
    }

    /**
     * @return Repository|\Illuminate\Foundation\Application|mixed
     */
    private function getCache()
    {
        return app(Repository::class);
    }
}
