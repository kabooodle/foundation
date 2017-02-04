<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\PageViews;
use Illuminate\Cache\Repository;

/**
 * Class PageViewablesTrait
 */
trait PageViewablesTrait
{
    /**
     * @return mixed
     */
    public function pageViews()
    {
        $cacheKey = get_called_class().'::'.$this->id;
        $cache = $this->getCache();

        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $results = $this->morphMany(PageViews::class, 'shoppable')->get();
        $cache->put($cacheKey, $results, 10);

        return $results;
    }

    /**
     * @return mixed
     */
    public function totalPageViews()
    {
        return $this->pageViews()->count();
    }

    /**
     * @return Repository|\Illuminate\Foundation\Application|mixed
     */
    private function getCache()
    {
        return app(Repository::class);
    }
}
