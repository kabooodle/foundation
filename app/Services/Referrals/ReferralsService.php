<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Referrals;

use Kabooodle\Models\User;
use Kabooodle\Models\Referrals;
use Kabooodle\Services\User\UserService;

/**
 * Class ReferralsService
 */
class ReferralsService
{
    const REFERRAL_BY_USERNAME = 'kbdl_referrer_username';

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param $username
     *
     * @return mixed
     */
    public function lookupRefereeByUsername($username)
    {
        return $this->userService->repository->getByUsername($username);
    }

    /**
     * @return null|User
     */
    public function getReferral()
    {
        return session(self::REFERRAL_BY_USERNAME);
    }
}
