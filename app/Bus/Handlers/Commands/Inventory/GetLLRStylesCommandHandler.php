<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\InventoryTypeStyles;
use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Bus\Commands\Inventory\GetLLRStylesCommand;

/**
 * Class GetLLRStylesCommandHandler
 */
class GetLLRStylesCommandHandler
{
    const TAG = 'kaboodle_llr_styles';

    /**
     * @param CacheRepository $cache
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param GetLLRStylesCommand $command
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function handle(GetLLRStylesCommand $command)
    {
        if ($this->cache->has(self::TAG)) {
            return $this->cache->get(self::TAG);
        }

        $styles = InventoryTypeStyles::whereHas('type', function ($q) {
            $q->where('slug', '=', 'lularoe');
        })->with('sizes')->orderBy('sort_order', 'asc')->get();

        $this->cache->add(self::TAG, $styles, 43800);

        return $styles;
    }
}
