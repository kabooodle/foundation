<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class CreateOrUpdateCustomListingCommand
 */
final class CreateOrUpdateCustomListingCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $saleName;

    /**
     * @var array
     */
    public $selectedInventoryItems;

    /**
     * @param User   $actor
     * @param string $saleName
     * @param array  $selectedInventoryItems
     */
    public function __construct(
        User $actor,
        string $saleName,
        array $selectedInventoryItems = []
    )
    {
        $this->actor = $actor;
        $this->saleName = $saleName;
        $this->selectedInventoryItems = $selectedInventoryItems;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getSaleName(): string
    {
        return $this->saleName;
    }

    /**
     * @return array
     */
    public function getSelectedInventoryItems(): array
    {
        return $this->selectedInventoryItems;
    }
}
