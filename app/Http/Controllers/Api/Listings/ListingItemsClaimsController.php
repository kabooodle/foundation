<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\User\AddGuestCommand;
use Kabooodle\Models\Email;
use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Models\User;

/**
 * Class ListingItemsClaimsController
 */
class ListingItemsClaimsController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param         $listingId
     * @param         $listingItemsId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $listingId, $listingItemsId)
    {
        try {
            $listingItem = $this->getListingItem($listingId, $listingItemsId);
            if (!$listingItem) {
                throw new ModelNotFoundException;
            }

            $this->dispatchNow(new ClaimInventoryItemCommand($this->getUser(), $listingItem,
                $listingItem->inventoryItem));

            return $this->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setData(['msg' => $e->getMessage()])->setStatusCode(500)->respond();
        } catch (Exception $e) {
            return $this->setData(['msg' => $e->getMessage()])->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $listingId
     * @param         $listingItemsId
     *
     * @return \Illuminate\Http\Response
     */
    public function guestStore(Request $request, $listingId, $listingItemsId)
    {
        try {
            $listingItem = $this->getListingItem($listingId, $listingItemsId);
            if (! $listingItem) {
                throw new ModelNotFoundException;
            }

            $this->validate($request, User::getGuestRules());

            // Does the email already exists in our system?
            $email = Email::whereAddress(trim($request->get('email')))->first();
            if ($email) {
                $this->dispatchNow(new ClaimInventoryItemCommand($email->user, $listingItem, $listingItem->inventoryItem, true, $email));
            } else {
                $guest = $this->dispatch(new AddGuestCommand(
                    $request->get('first_name'),
                    $request->get('last_name'),
                    $request->get('email'),
                    $request->get('company'),
                    $request->get('street1'),
                    $request->get('street2'),
                    $request->get('city'),
                    $request->get('state'),
                    $request->get('zip'),
                    $request->get('phone')
                ));

                $this->dispatchNow(new ClaimInventoryItemCommand($guest, $listingItem, $listingItem->inventoryItem, true, $guest->primaryEmail));
            }

            return $this->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setData(['msg' => $e->getMessage()])->setStatusCode(500)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setData(['msg' => $e->getMessage()])->setStatusCode(500)->respond();
        }
    }

    /**
     * @param $listingId
     * @param $listingItemId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    private function getListingItem($listingId, $listingItemId)
    {
        return ListingItems::where('id', $listingItemId)->where('listing_id', $listingId)->first();
    }
}
