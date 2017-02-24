<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Listings;

use Kabooodle\Models\User;
use Kabooodle\Models\AbstractListingModel;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Kabooodle\Foundation\Exceptions\Listings\ListingExceedsHourlyLimitException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class ListingsService
 */
class ListingsService
{
    /**
     * @var FacebookSdkService
     */
    public $facebookService;

    /**
     * @param FacebookSdkService $facebookSdkService
     */
    public function __construct(FacebookSdkService $facebookSdkService)
    {
        $this->facebookService = $facebookSdkService;
    }

    /**
     * @param $claimableDate
     * @param $scheduledDate
     *
     * @throws ListingClaimableDateIsBeforeListingDateException
     */
    public function assertListingClaimableDateIsBeforeListingDateException($claimableDate, $scheduledDate)
    {
        if ($claimableDate && strtotime($claimableDate) < strtotime($scheduledDate)) {
            throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
        }
    }

    /**
     * @param User $user
     * @param      $startTime
     * @param      $endTime
     * @param      $itemsCount
     *
     * @throws ListingExceedsHourlyLimitException
     */
    public function assertNumberOfItemsDoesNotExceedFacebookHourlyQuota(User $user, $startTime, $endTime, $itemsCount)
    {
        $exceeds = AbstractListingModel::checkIfAttemptedListingExceedsHourlyQuota(
            $user->id,
            $startTime,
            $endTime,
            $itemsCount
        );

        if ($exceeds) {
            $amount = AbstractListingModel::queryGetItemsDuringDateTimeBlockForUser(
                $user->id,
                $startTime,
                $endTime
            );

            $exception = new ListingExceedsHourlyLimitException;
            $exception->setTotalForHour($amount);

            throw $exception;
        }
    }

    /**
     * @param User $user
     *
     * @throws FacebookTokenInvalidException
     */
    public function assertFacebookAccessTokenIsValid(User $user)
    {
        if (!$this->facebookService->testAccessToken($user->getFacebookUserToken())) {
            $user->facebook_access_token = null;
            $user->facebook_access_token_expires = null;
            $user->save();

            throw new FacebookTokenInvalidException;
        }
    }
}
