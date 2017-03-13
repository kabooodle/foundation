<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Console\Commands\Subscription;

use DB;
use Kabooodle\Services\User\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Subscriptions\TrialAccountExpiring;

/**
 * Class TrialExpiring
 */
class TrialExpiring extends AbstractConsoleNotification
{
    use DispatchesJobs;

    /**
     * @var string
     */
    protected $signature = 'expiring:trials';

    /**
     * @var UserService
     */
    static $service;

    public function handle()
    {
        /** @var UserService $service */
        $service = $this->getUserService();

        // Get accounts expiring within the next [lookahead_days]
        $trialAccounts = $service->repository->getTrialAccountsNotNotified($service::LOOKAHEAD_DAYS);

        if ($trialAccounts->count() === 0) {
            $this->warn('No notifiable trial accounts expiring within the next ' . $service::LOOKAHEAD_DAYS . ' days.');

            return;
        }

        $this->info('Processing ' . $trialAccounts->count() . ' trial accounts.');

        foreach ($trialAccounts as $trialAccount) {
            event(new TrialAccountExpiring($trialAccount));

            $this->logNotificationHandled($trialAccount);
        }
    }

    /**
     * @return \Illuminate\Foundation\Application|UserService
     */
    public function getUserService()
    {
        if (!self::$service) {
            self::$service = app()->make(UserService::class);
        }

        return self::$service;
    }
}
