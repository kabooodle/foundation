<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Repositories\User;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface UserRepositoryInterface
 * @package Kabooodle\Repositories\User
 */
interface UserRepositoryInterface
{
    /**
     * @param int $lookahead
     * @return Collection
     */
    public function getTrialAccountsNotNotified(int $lookahead);

    /**
     * @param array $usernames
     *
     * @return mixed
     */
    public function getByUsernames(array $usernames);

    /**
     * @param string $username
     *
     * @return mixed
     */
    public function getByUsername(string $username);
}