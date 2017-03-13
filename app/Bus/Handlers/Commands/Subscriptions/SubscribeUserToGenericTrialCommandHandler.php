<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Subscriptions;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\GenericTrialHistory;
use Kabooodle\Bus\Events\User\UserUpgradedToGenericTrial;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToGenericTrialCommand;
use Kabooodle\Foundation\Exceptions\Subscription\UserAlreadyHadFreeTrialException;

/**
 * Class SubscribeUserToGenericTrial
 */
class SubscribeUserToGenericTrialCommandHandler
{
    /**
     * @param SubscribeUserToGenericTrialCommand $command
     */
    public function handle(SubscribeUserToGenericTrialCommand $command)
    {
        $user = $command->getUser();
        $this->assertUserNotAlreadyHadGenericTrial($user);

        DB::transaction(function() use ($user, $command) {
            $endsAt = Carbon::now()->addDays($command->getTrialDurationInDays());

            $user->trial_ends_at = $endsAt;
            $user->save();

            GenericTrialHistory::create([
                'user_id' => $user->id,
                'trial_ends_at' => $endsAt
            ]);

            event(new UserUpgradedToGenericTrial($user));
        });
    }

    /**
     * @param User $user
     *
     * @throws UserAlreadyHadFreeTrialException
     */
    public function assertUserNotAlreadyHadGenericTrial(User $user)
    {
        if ($user->hasUserAlreadyHadGenericTrial()) {
            throw new UserAlreadyHadFreeTrialException;
        }
    }
}
