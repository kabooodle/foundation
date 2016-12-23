<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Subscriptions;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\GenericTrialHistory;
use Kabooodle\Bus\Events\User\UserUpgradedToGenericTrial;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToGenericTrialCommand;

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
        if (! $this->hasUserAlreadyHadGenericTrial($user)) {
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
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUserAlreadyHadGenericTrial(User $user) : bool
    {
        return $user->hasUserAlreadyHadGenericTrial();
    }
}
