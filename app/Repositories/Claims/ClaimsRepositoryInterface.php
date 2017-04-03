<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Repositories\Claims;

/**
 * Interface ClaimsRepositoryInterface
 */
interface ClaimsRepositoryInterface
{
    /**
     * @param int   $userId
     * @param array $claimIds
     *
     * @return mixed
     */
    public function getAllClaimsOnUserListables(int $userId, array $claimIds = []);

    /**
     * @param int   $userId
     * @param array $claimIds
     *
     * @return mixed
     */
    public function accept(int $userId, array $claimIds);

    /**
     * @param int   $userId
     * @param array $claimIds
     *
     * @return mixed
     */
    public function reject(int $userId, array $claimIds);

    /**
     * @param int   $userId
     * @param array $claimIds
     * @param array $labels
     *
     * @return mixed
     */
    public function label(int $userId, array $claimIds, array $labels);
}
