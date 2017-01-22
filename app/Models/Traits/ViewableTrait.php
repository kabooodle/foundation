<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        $cacheKey = get_called_class().'::'.$this->id;
        $cache = $this->getCache();

        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $results = $this->morphMany(View::class, 'viewable')->get();
        $cache->put($cacheKey, $results, 10);

        return $results;
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
