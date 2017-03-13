<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Listings;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Models\AbstractListingModel;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Repositories\Listings\ListingsRepositoryInterface;
use Kabooodle\Foundation\Exceptions\FacebookTokenInvalidException;
use Kabooodle\Foundation\Exceptions\Listings\ListingExceedsHourlyLimitException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class ListingsService
 */
class ListingsService
{
    const HOURLY_QUOTA_LISTING_LIMIT = 700;
    const SCHEDULER_LOOKAHEAD_FROM_NOW_SECONDS = 299; // 4 minutes, 49 seconds

    /**
     * @var int
     */
    protected $totalScheduledResultsFound;

    /**
     * @var ListingsRepositoryInterface
     */
    public $listingRepository;

    /**
     * @var FacebookSdkService
     */
    public $facebookService;

    /**
     * @param ListingsRepositoryInterface $listingsRepository
     * @param FacebookSdkService          $facebookSdkService
     */
    public function __construct(ListingsRepositoryInterface $listingsRepository, FacebookSdkService $facebookSdkService)
    {
        $this->listingsRepository = $listingsRepository;
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
     * @param      $itemsCount
     *
     * @return bool
     */
    public function checkNumberOfItemsDoesNotExceedFacebookHourlyQuota(User $user, $startTime, int $itemsCount)
    {
        \Log::info('Checking items ['.$itemsCount.'] for FB quota on ['. $startTime.']');
        $results = AbstractListingModel::queryGetItemsDuringDateTimeBlockForUser($user->id, $startTime);
        $countResults = $this->totalScheduledResultsFound = count($results);

        return ($countResults + $itemsCount) > self::HOURLY_QUOTA_LISTING_LIMIT;
    }

    /**
     * @param User $user
     * @param      $startTime
     * @param      $itemsCount
     *
     * @throws ListingExceedsHourlyLimitException
     */
    public function assertNumberOfItemsDoesNotExceedFacebookHourlyQuota(User $user, $startTime, $itemsCount)
    {
        if ($this->checkNumberOfItemsDoesNotExceedFacebookHourlyQuota($user, $startTime, $itemsCount)) {
            $exception = new ListingExceedsHourlyLimitException;
            $exception->setMaximumTotalAllowedForHour(self::HOURLY_QUOTA_LISTING_LIMIT);
            $exception->setTotalExistingListings($this->totalScheduledResultsFound);

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

    /**
     * @param User   $user
     * @param Carbon $startTime
     * @param int    $itemsCount
     *
     * @return Carbon
     */
    public function findAvailableTimeToScheduleDeletion(User $user, Carbon $startTime, int $itemsCount)
    {
        if (! $this->checkNumberOfItemsDoesNotExceedFacebookHourlyQuota($user, $startTime->toDateTimeString(), $itemsCount)) {
            return $startTime;
        }

        return $this->findAvailableTimeToScheduleDeletion($user, $startTime->addMinutes(60), $itemsCount);
    }

    /**
     * @return mixed
     */
    public function getScheduledForDeletionListings()
    {
        $cachedNow = Carbon::now()->getTimestamp();
        $startTime = Carbon::createFromTimestamp($cachedNow);
        $endTime = Carbon::createFromTimestamp($cachedNow)->addSeconds(self::SCHEDULER_LOOKAHEAD_FROM_NOW_SECONDS);

        return Listings::getScheduledForDeletionListings($startTime, $endTime);
    }

    /**
     * @return mixed
     */
    public function getScheduledForDeletionListingItems()
    {
        $cachedNow = Carbon::now()->getTimestamp();
        $startTime = Carbon::createFromTimestamp($cachedNow);
        $endTime = Carbon::createFromTimestamp($cachedNow)->addSeconds(self::SCHEDULER_LOOKAHEAD_FROM_NOW_SECONDS);

        return ListingItems::getScheduledForDeletionListingItems($startTime, $endTime);
    }
}
