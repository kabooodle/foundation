<?php

namespace Kabooodle\Services\User;

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
}
