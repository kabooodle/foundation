<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

/**
 * Created by PhpStorm.
 * User: Jake-FER
 * Date: 2/3/17
 * Time: 7:10 PM
 */

namespace Kabooodle\Repositories\User;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Console\Commands\Subscription\TrialExpiring;

/**
 * Class UserRepository
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $lookahead
     *
     * @return Collection
     */
    public function getTrialAccountsNotNotified(int $lookahead)
    {
        return $this->model->leftJoin('notification_logs', 'notification_logs.user_id', '=', 'users.id')
            ->where('notification_logs.notificationable_type', '=', TrialExpiring::class)
            ->whereRaw('users.trial_ends_at >= DATE(NOW())')
            ->whereRaw('users.trial_ends_at <= DATE_ADD(DATE(NOW()), INTERVAL ' . $lookahead . ' DAY)')
            ->whereRaw('users.id IS NOT NULL')
            ->select(['users.id', DB::raw('count(notification_logs.id) as count')])
            ->havingRaw(DB::raw('count = 0'))
            ->havingRaw(DB::Raw('users.id IS NOT NULL'))
            ->get();
    }

    /**
     * @param array $usernames
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getByUsernames(array $usernames)
    {
        return $this->model->whereIn('username', $usernames)
            ->groupBy('id')
            ->get();
    }

    /**
     * @param string $username
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getByUsername(string $username)
    {
        return $this->model->where('username', '=', $username)->first();
    }
}