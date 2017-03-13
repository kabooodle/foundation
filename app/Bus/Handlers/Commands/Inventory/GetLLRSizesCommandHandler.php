<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\InventorySizes;
use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Bus\Commands\Inventory\GetLLRSizesCommand;

/**
 * Class GetLLRSizesCommandHandler
 */
class GetLLRSizesCommandHandler
{
    const TAG = 'kaboodle_llr_sizes';

    /**
     * @param CacheRepository $cache
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param GetLLRSizesCommand $command
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function handle(GetLLRSizesCommand $command)
    {
        if ($this->cache->has(self::TAG)) {
            return $this->cache->get(self::TAG);
        }

        $sizes = InventorySizes::orderByRaw('length(name)')->orderBy('name')->get();

        $this->cache->add(self::TAG, $sizes, 43800);

        return $sizes;
    }
}
