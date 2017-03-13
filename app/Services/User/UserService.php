<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\User;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Repositories\User\UserRepositoryInterface;

/**
 * Class UserService
 */
class UserService
{
    const LOOKAHEAD_DAYS = 7;

    /**
     * @var UserRepositoryInterface
     */
    public $repository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User   $user
     * @param Carbon $date
     *
     * @return bool
     */
    public function checkDateIsBeforeFacebookTokenExpires(User $user, Carbon $date)
    {
        // Check the date occurs before the FB token's expiration date.
    }
}
