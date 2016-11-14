<?php

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;

/**
 * Class GetUsedInventoryCategoriesCommand
 */
final class GetUsedInventoryCategoriesCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @param User $actor
     */
    public function __construct(User $actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }
}