<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class ScheduleFlashsaleListingcommand
 */
final class ScheduleFlashsaleListingcommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $flashSaleId;

    /**
     * @var array
     */
    public $selectedItems;

    /**
     * @param User                   $actor
     * @param int|null               $flashSaleId
     * @param array                  $selectedItems
     */
    public function __construct(
        User $actor,
        int $flashSaleId = null,
        array $selectedItems = []
    )
    {
        $this->actor = $actor;
        $this->flashSaleId = $flashSaleId;
        $this->selectedItems = $selectedItems;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return int
     */
    public function getFlashSaleId(): int
    {
        return $this->flashSaleId;
    }

    /**
     * @return array
     */
    public function getSelectedItems(): array
    {
        return $this->selectedItems;
    }
}
