<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

/**
 * Class GetInventoryTypesCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
final class GetInventoryTypesCommand
{
    /**
     * @var array
     */
    public $slugs = [];

    /**
     * GetInventoryTypesCommand constructor.
     *
     * @param array $onlyTheseSlugs
     */
    public function __construct(array $onlyTheseSlugs = [])
    {
        $this->slugs = $onlyTheseSlugs;
    }

    /**
     * @return array
     */
    public function getSlugs()
    {
        return $this->slugs;
    }
}
