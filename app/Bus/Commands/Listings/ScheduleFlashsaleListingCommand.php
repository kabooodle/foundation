<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class ScheduleFlashsaleListingcommand
 */
final class ScheduleFlashsaleListingCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var array
     */
    public $flashSales;

    /**
     * @param User  $actor
     * @param array $flashSales
     */
    public function __construct(User $actor, array $flashSales)
    {
        $this->actor = $actor;
        $this->flashSales = $flashSales;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return array
     */
    public function getFlashSales(): array
    {
        return $this->flashSales;
    }
}
