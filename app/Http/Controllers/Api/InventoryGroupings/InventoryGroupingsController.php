<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\InventoryGroupings;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\InventoryGroupings\CreateInventoryGroupingCommand;
use Kabooodle\Bus\Commands\InventoryGroupings\DestroyInventoryGroupingCommand;
use Kabooodle\Bus\Commands\InventoryGroupings\UpdateInventoryGroupingCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Foundation\Exceptions\ForbiddenModelAccessException;
use Kabooodle\Foundation\Exceptions\ForbiddenUserAccessException;
use Illuminate\Validation\ValidationException;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;

/**
 * Class InventoryGroupingsController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryGroupingsController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $username)
    {
        $user = User::whereUsername($username)->firstOrFail();
        $data = [
            'user' => $user,
            'groupings' => $user->inventoryGroupings,
        ];

        return $this->setData($data)->respond();
    }

    /**
     * @param Request $request
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $username, $groupingId)
    {
        $user = User::whereUsername($username)->firstOrFail();
        $data = [
            'user' => $user,
            'groupings' => $user->inventoryGroupings->find($groupingId),
        ];

        return $this->setData($data)->respond();
    }

    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $username)
    {
        try {
            $this->checkIds($username);

            $this->validate($request, InventoryGrouping::getRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' => 'You must add at least 1 image.']);

            $grouping = $this->dispatchNow(new CreateInventoryGroupingCommand(
                $this->getUser(),
                Binput::get('name'),
                (bool)Binput::get('locked'),
                (float)Binput::get('price_usd'),
                (int)Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('inventory_ids'),
                Binput::get('description'),
                implode(',', Binput::get('categories', []))
            ));

            return $this->setData(['msg' => 'Grouping {$grouping->name} created', 'grouping' => $grouping->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => $e])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $username, $groupingId)
    {
        try {
            $grouping = $this->checkIds($username, $groupingId);

            $this->validate($request, InventoryGrouping::getRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' => 'You must add at least 1 image.']);

            $updated = $this->dispatchNow(new UpdateInventoryGroupingCommand(
                $this->getUser(),
                $grouping,
                Binput::get('name'),
                (bool)Binput::get('locked'),
                (float)Binput::get('price_usd'),
                (int)Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('inventory_ids'),
                Binput::get('description'),
                implode(',', Binput::get('categories', []))
            ));

            return $this->setData(['msg' => "Item {$updated->name} created", 'grouping' => $updated->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => $e])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $username, $groupingId)
    {
        try {
            $grouping = $this->checkIds($username, $groupingId);

            $this->dispatchNow(new DestroyInventoryGroupingCommand($this->user(), $grouping));

            return $this->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
        }
    }

//    /**
//     * @param Request $request
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function associate(Request $request)
//    {
//        $listingType = Binput::get('listingtype');
//        $flashsaleId = Binput::get('flashsale', null);
//        $selectedItems = (array) Binput::get('items', []);
//
//        // Facebook data
//        $facebookAlbums = (array) Binput::get('fb_albums', []);
//        $facebookGroup = Binput::get('fb_group', null);
//        $facebookGroupId = $facebookGroup ? $facebookGroup['id'] : null;
//
//        // Facebook sales options, are, optional :)
//        $options = (array) Binput::get('options', []);
//
//        // Date to list it and remove it
//        $listAt = array_get($options, 'list_at', null);
//        $removeAt = array_get($options,'remove_at', null);
//        // Date range you can claim.
//        $claimableAt = array_get($options, 'available_at', null);
//        $claimableUntil = array_get($options, 'available_until', null);
//        $itemMessage = array_get($options, 'item_message', false);
//
//        try {
//            // You must have either a flashsaleid or facebookalbum
//            if (($listingType == 'flashsale' && ! $flashsaleId ) || ($listingType == 'facebook' && count($facebookAlbums) == 0)) {
//                throw new MissingMandatoryParametersException('You must select a sale');
//            }
//
//            if (count($selectedItems) == 0) {
//                throw new MissingMandatoryParametersException;
//            }
//
//            if ($claimableAt && strtotime($claimableAt) < strtotime($listAt)) {
//                throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
//            }
//
//            $listingOptions = new FacebookListingOptions($listAt, $removeAt, $claimableAt, $claimableUntil, $itemMessage);
//
//            if ($listingType == 'flashsale' && $flashsaleId) {
//                $command = new ScheduleFlashsaleListingcommand($this->getUser(), $flashsaleId, $selectedItems);
//            } else {
//                $command = new ScheduleFacebookListingCommand(
//                    $this->getUser(),
//                    $facebookAlbums,
//                    $facebookGroupId,
//                    $selectedItems,
//                    $listingOptions
//                );
//            }
//
//            $this->dispatchNow($command);
//
//            return $this->setData(['msg' =>'Items scheduled successfully to sale.'])->respond();
//        } catch (FacebookAuthenticationException $e) {
//            $msg = 'Your facebook credentials are invalid. Please re-authorize '.env('APP_NAME').' for your facebook account, via our settings page.';
//            return $this->setData(['msg' => $msg])->setStatusCode(500)->respond();
//        }catch (MissingMandatoryParametersException $e) {
//            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage() ? : 'You must select as least 1 item for listing.'])->respond();
//        } catch (ListingConflictsWithExistingListingException $e) {
//            return $this->setStatusCode(500)->setData(['msg' => 'The date and time block you selected conflicts with an existing listing. Please select a new block of time.'])->respond();
//        } catch (Exception $e){
//            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
//        }
//    }
//
//    /**
//     * @param Request $request
//     * @param         $username
//     * @param         $flashSaleItemId
//     *
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function destroyAssociation(Request $request, $username, $flashSaleItemId)
//    {
//        $this->dispatchNow(new DeleteInventoryFromSaleCommand($this->getUser(), $flashSaleItemId));
//
//        return $this->noContent();
//    }

    /**
     * @param $username
     * @param null $modelId
     *
     * @return mixed
     * @throws ForbiddenModelAccessException
     * @throws ForbiddenUserAccessException
     */
    private function checkIds($username, $modelId = null)
    {
        if ($this->user()->username !== $username) {
            throw new ForbiddenUserAccessException;
        }

        if ($modelId) {
            $model = InventoryGrouping::whereId($modelId)->firstOrFail();
            if ($this->user()->id !== $model->user_id) {
                throw new ForbiddenModelAccessException;
            }
            return $model;
        }
    }
}
