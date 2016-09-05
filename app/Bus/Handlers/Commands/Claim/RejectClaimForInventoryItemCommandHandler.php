<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Claim;

use Carbon\Carbon;
use Kabooodle\Models\Claims;
use Kabooodle\Bus\Commands\Claim\RejectClaimForInventoryItemCommand;

/**
 * Class RejectClaimForInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Claim\
 */
class RejectClaimForInventoryItemCommandHandler
{
    /**
     * @param RejectClaimForInventoryItemCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function handle(RejectClaimForInventoryItemCommand $command)
    {
        $claim = Claims::where('uuid', $command->getClaimId())->first();
        $claim->rejected_by = user()->id;
        $claim->rejected_on = Carbon::now();
        $claim->rejected_reason = $command->getNotes();
        $claim->accepted = false;
        $claim->save();

        return $claim;
    }
}