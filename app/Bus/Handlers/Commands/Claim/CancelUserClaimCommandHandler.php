<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Claim\CancelUserClaimCommand;
use Kabooodle\Bus\Events\Claim\ClaimWasCanceledEvent;
use Kabooodle\Foundation\Exceptions\Claim\UserClaimNotCancelableException;

/**
 * Class CancelUserClaimCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim
 */
class CancelUserClaimCommandHandler
{
    use DispatchesJobs;

    /**
     * @param CancelUserClaimCommand $command
     *
     * @return mixed
     * @throws UserClaimNotCancelableException
     */
    public function handle(CancelUserClaimCommand $command)
    {
        $claim = $command->getClaim();
        if (!$claim->isCancelable()) {
            throw new UserClaimNotCancelableException('Your claim can no longer be canceled.');
        }
        return DB::transaction(function () use ($claim, $command) {
            $claim->cancel_message = $command->getMessage();
            $claim->cancel();

            event(new ClaimWasCanceledEvent($claim));

            return !is_null($claim->canceled_at);
        });
    }
}
