<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Likeable;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\LikeableInterface;

/**
 * Class LikeEntityCommand
 * @package Kabooodle\Bus\Commands\Likeable
 */
class LikeEntityCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var LikeableInterface
     */
    private $entity;

    /**
     * LikeEntityCommand constructor.
     *
     * @param User              $user
     * @param LikeableInterface $entity
     */
    public function __construct(User $user, LikeableInterface $entity)
    {
        $this->user = $user;
        $this->entity = $entity;
    }

    /**
     * @return LikeableInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
