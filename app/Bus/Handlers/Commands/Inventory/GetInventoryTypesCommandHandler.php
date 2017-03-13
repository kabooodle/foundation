<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\InventoryType;
use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Bus\Commands\Inventory\GetInventoryTypesCommand;

/**
 * Class GetInventoryTypesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class GetInventoryTypesCommandHandler
{
    const TAG = 'kaboodle_llr_styles_and_sizes';

    /**
     * GetActiveNotificationsHandler constructor.
     *
     * @param CacheRepository $cache
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param GetInventoryTypesCommand $command
     *
     * @return mixed
     */
    public function handle(GetInventoryTypesCommand $command)
    {
        if ($this->cache->has(self::TAG)) {
            return $this->cache->get(self::TAG);
        }

        $types = InventoryType::withStylesAndSizes()->get();
        $this->cache->add(self::TAG, $types, 43800);

        if ($command->getSlugs() && count($command->getSlugs()) > 0) {
            return $types->filter(function ($type) use ($command) {
                return in_array($type->slug, $command->getSlugs());
            });
        }

        return $types;
    }
}
