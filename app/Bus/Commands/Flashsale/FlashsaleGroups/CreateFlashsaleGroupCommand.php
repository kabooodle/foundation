<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale\FlashsaleGroups;

use Kabooodle\Models\User;

/**
 * Class CreateFlashsaleGroupCommand
 */
final class CreateFlashsaleGroupCommand
{
    /**
     * @var User
     */
    private $actor;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    public $userIds;

    /**
     * @param User   $actor
     * @param string $name
     * @param array  $userIds
     */
    public function __construct(User $actor, string $name, array $userIds )
    {
        $this->actor = $actor;
        $this->name = $name;
        $this->userIds = $userIds;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }
}
