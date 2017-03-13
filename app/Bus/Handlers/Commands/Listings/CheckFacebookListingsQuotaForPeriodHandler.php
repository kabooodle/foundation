<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Kabooodle\Models\AbstractListingModel;
use Kabooodle\Bus\Commands\Listings\CheckFacebookListingsQuotaForPeriod;
use Kabooodle\Foundation\Exceptions\Listings\ListingExceedsHourlyLimitException;

/**
 * Class CheckFacebookListingsQuotaForPeriodHandler
 */
class CheckFacebookListingsQuotaForPeriodHandler
{
    /**
     * @param CheckFacebookListingsQuotaForPeriod $command
     *
     * @throws ListingExceedsHourlyLimitException
     */
    public function handle(CheckFacebookListingsQuotaForPeriod $command)
    {
        $exceeds = AbstractListingModel::checkIfAttemptedListingExceedsHourlyQuota($command->getActor()->id,
            $command->getStartTime(), $command->getEndTime(), $command->getIncomingItemsCount());

        if ($exceeds) {
            $amount = AbstractListingModel::queryGetItemsDuringDateTimeBlockForUser($command->getActor()->id,
                $command->getStartTime(), $command->getEndTime());

            $exception = new ListingExceedsHourlyLimitException;
            $exception->setTotalForHour($amount);

            throw $exception;
        }
    }
}
