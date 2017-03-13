<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use Kabooodle\Models\ShippingParcelTemplates;
use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Bus\Commands\Shipping\GetShippingParcelTemplatesCommand;

/**
 * Class GetShippingParcelTemplatesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class GetShippingParcelTemplatesCommandHandler
{
    const TAG = 'kaboodle_shipping_packaging_templates';

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
     * @param GetShippingParcelTemplatesCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function handle(GetShippingParcelTemplatesCommand $command)
    {
        if ($this->cache->has(self::TAG)) {
            return $this->cache->get(self::TAG);
        }

        $templates = ShippingParcelTemplates::orderBy('name')->where('active', 1)->get();
        $this->cache->add(self::TAG, $templates, 43800);

        return $templates;
    }
}
