<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\Claims;
use Kabooodle\Bus\Events\Claim\ClaimWasRejectedEvent;
use Kabooodle\Bus\Commands\Claim\RejectClaimForClaimableItemCommand;

/**
 * Class RejectClaimForClaimableItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim\
 */
class RejectClaimForClaimableItemCommandHandler
{
    /**
     * @param RejectClaimForClaimableItemCommand $command
     *
     * @return mixed|Claims
     */
    public function handle(RejectClaimForClaimableItemCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $claim = Claims::where('uuid', $command->getClaimUuid())->firstOrFail();
            $claim->rejected_by = $command->getUser()->id;
            $claim->rejected_on = Carbon::now();
            $claim->rejected_reason = $command->getNotes();
            $claim->accepted = false;
            $claim->save();

            if ($claim->isVerified()) {
                $claim->listable->incrementInitialQty(1);
            }

            event(new ClaimWasRejectedEvent($command->getUser(), $claim));

            return $claim;
        });
    }
}
