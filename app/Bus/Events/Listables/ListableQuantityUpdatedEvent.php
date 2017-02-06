<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listables;

use Kabooodle\Models\Contracts\Listable;

/**
 * Class ListableQuantityUpdatedEvent
 */
final class ListableQuantityUpdatedEvent
{
    /**
     * @var Listable
     */
    public $listableItem;

    /**
     * @param Listable $listableItem
     */
    public function __construct(Listable $listableItem)
    {
        $this->listableItem = $listableItem;
    }

    /**
     * @return Listable
     */
    public function getListableItem(): Listable
    {
        return $this->listableItem;
    }
}
