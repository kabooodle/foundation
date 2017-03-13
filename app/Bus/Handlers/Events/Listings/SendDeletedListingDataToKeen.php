<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listings;

use Bugsnag;
use Exception;
use Kabooodle\Services\Keen\KeenService;
use Kabooodle\Bus\Events\Listings\ListingWasDeleted;

/**
 * Class SendDeletedListingDataToKeen
 */
class SendDeletedListingDataToKeen
{
    /**
     * @var KeenService
     */
    public $keenService;

    /**
     * @param KeenService $keenService
     */
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

    /**
     * @param ListingWasDeleted $event
     */
    public function handle(ListingWasDeleted $event)
    {
        try {

//            $this->keenService->keenClient->deleteEvents('', []);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
