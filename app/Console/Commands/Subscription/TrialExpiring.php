<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Console\Commands\Subscription;

use DB;
use Kabooodle\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Subscriptions\TrialAccountExpiring;

/**
 * Class TrialExpiring
 * @package Kabooodle\Console\Commands\Subscription
 */
class TrialExpiring extends AbstractConsoleNotification
{
    use DispatchesJobs;

    const LOOKAHEAD_DAYS = 7;

    /**
     * @var string
     */
    protected $signature = 'expiring:trials';

    public function handle()
    {
        // Get accounts expiring within the next [lookahead_days]
        $trialAccounts = $this->getTrialAccounts();

        if ($trialAccounts->count() === 0) {
            $this->line('No trial accounts expiring within the next ' . self::LOOKAHEAD_DAYS . ' days.');

            return;
        }

        foreach ($trialAccounts as $trialAccount) {
            event(new TrialAccountExpiring($trialAccount, self::LOOKAHEAD_DAYS));

            $this->logNotificationHandled($trialAccount);
        }
    }

    /**
     * @param int $lookahead
     *
     * @return Collection
     */
    public function getTrialAccounts(int $lookahead = self::LOOKAHEAD_DAYS)
    {
        return User::leftJoin('notification_logs', 'notification_logs.user_id', '=', 'users.id')
            ->where('notification_logs.notificationable_type', '=', get_class($this))
            ->whereRaw('users.trial_ends_at >= DATE(NOW())')
            ->whereRaw('users.trial_ends_at <= DATE_ADD(DATE(NOW()), INTERVAL ' . $lookahead . ' DAY)')
            ->whereRaw('users.id IS NOT NULL')
            ->select(['users.id', DB::raw('count(notification_logs.id) as count')])
            ->havingRaw(DB::raw('count = 0'))
            ->havingRaw(DB::Raw('users.id IS NOT NULL'))
            ->get();
    }
}
