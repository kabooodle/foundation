<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listables;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\ListableInterface;

/**
 * Class ActivateListableCommand
 */
final class ActivateListableCommand
{
    /**
     * @var ListableInterface
     */
    public $listable;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param ListableInterface $listable
     * @param User              $actor
     */
    public function __construct(ListableInterface $listable, User $actor)
    {
        $this->listable = $listable;
        $this->actor = $actor;
    }

    /**
     * @return ListableInterface
     */
    public function getListable(): ListableInterface
    {
        return $this->listable;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }
}
