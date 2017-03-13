<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Kabooodle\Models\ShippingQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Bus\Commands\Claim\ToggleShippingMethodOnClaimCommand;

/**
 * Class ToggleShippingMethodOnClaimCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class ToggleShippingMethodOnClaimCommandHandler
{
    /**
     * @param ToggleShippingMethodOnClaimCommand $command
     *
     * @return mixed
     */
    public function handle(ToggleShippingMethodOnClaimCommand $command)
    {
        $user = $command->getActor();
        $claimId = $command->getClaimId();
        $method = $command->getNewShippingMethod();
        /** @var Claims $claim */
        $claim = $user->claimsAsSellerNoShipping()->find($claimId);
        if (!$claim) {
            throw new ModelNotFoundException;
        }

        return DB::transaction(function () use ($claim, $method, $user) {
            if ($method == 'kabooodle') {
                return $this->switchToKabooodle($claim, $user);
            }

            return $this->switchToManual($claim);
        });
    }

    /**
     * @param Claims $claim
     *
     * @return Claims
     */
    public function switchToManual(Claims $claim)
    {
        $claim->shipped_manually = true;
        $claim->shipped_manually_on = Carbon::now();
        $claim->shippingQueue()->delete();
        $claim->save();

        return $claim;
    }

    /**
     * @param Claims $claim
     * @param User   $user
     *
     * @return Claims
     */
    public function switchToKabooodle(Claims $claim, User $user)
    {
        $claim->shipped_manually = false;
        $claim->shipped_manually_on = null;
        ShippingQueue::create([
            'user_id' => $user->id,
            'claim_id' =>$claim->id
        ]);
        $claim->save();

        return $claim;
    }
}
