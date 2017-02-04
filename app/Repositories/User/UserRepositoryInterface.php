<?php

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
}