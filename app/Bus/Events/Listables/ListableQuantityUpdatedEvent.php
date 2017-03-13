<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listables;

use Kabooodle\Models\Contracts\ListableInterface;

/**
 * Class ListableQuantityUpdatedEvent
 */
final class ListableQuantityUpdatedEvent
{
    /**
     * @var ListableInterface
     */
    public $listableItem;

    /**
     * @param ListableInterface $listableItem
     */
    public function __construct(ListableInterface $listableItem)
    {
        $this->listableItem = $listableItem;
    }

    /**
     * @return ListableInterface
     */
    public function getListableItem(): ListableInterface
    {
        return $this->listableItem;
    }
}
