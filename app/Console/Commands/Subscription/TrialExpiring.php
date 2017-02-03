<?php

namespace Kabooodle\Console\Commands\Subscription;

use Kabooodle\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

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
        // TODO: Finish
        // Get accounts expiring within the next [lookahead_days]
        $trialAccounts = $this->getTrialAccounts();

        if ($trialAccounts->count() === 0) {
            $this->line('No trial accounts expiring within the next '.self::LOOKAHEAD_DAYS.' days.');
            return;
        }

        // Notify the users via email
        // Notify the users via DB
        // Flag or log the notification for records.
    }

    /**
     * @param int $lookahead
     * @return Collection
     */
    public function getTrialAccounts(int $lookahead = self::LOOKAHEAD_DAYS)
    {
        return User::whereRaw('expires_on >= DATE(NOW())')
            ->whereRaw('expires_on <= DATE_ADD(DATE(NOW()), INTERVAL '.$lookahead.' DAY)')
            ->get();
    }
}
