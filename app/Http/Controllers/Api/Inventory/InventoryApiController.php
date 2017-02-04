<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Illuminate\Validation\ValidationException;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingcommand;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class InventoryApiController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryApiController extends AbstractApiController
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Begin the user inventory query.
        $groupings = [];
        $data = $this->getUser()->inventory()->NoEagerLoads()->with(['style', 'styleSize', 'files']);
        $data = $data->get()->groupBy('inventory_type_styles_id');
        foreach($data as $styleId => $items) {
            $groupings[$styleId] = [
                'name' => null,
                'total' => $items->count(),
                'id' => $styleId,
            ];
            if ($items->count() > 0) {
                foreach($items as $item) {
                    if(! $groupings[$styleId]['name']) {
                        $groupings[$styleId]['name'] = $item->style->name;
                    }
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['id'] = $item->styleSize->id;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['name'] = $item->styleSize->name;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty'] : $item->initial_qty;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['items'][] = [
                        'id' => $item->id,
                        'name_uuid' => $item->name_uuid,
                        'uuid' => $item->uuid,
                        'size_id' => $item->styleSize->id,
                        'size_name' => $item->styleSize->name,
                        'style_id' => $styleId,
                        'style_name' => $item->style->name,
                        'images' => $item->files->toArray(),
                        'initial_qty' => $item->initial_qty,
                        'price_usd' => $item->price_usd,
                        'files' => $item->files,
                        'cover_photo' => $item->cover_photo,
                        'listable_item_class' => get_class($item),
                        'listing_item_class' => $item->getListingItemClass(),
                    ];
                }

                // Sort based on the order key.
                usort($groupings[$styleId]['sizes'], function ($item1, $item2) {
                    return $item1['order'] <=> $item2['order'];
                });
            }
        }

        sort($groupings);

        return $this->setData($groupings)->respond();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $item = $this->getUser()->inventory()->findOrFail($id);
            $this->validate($request, Inventory::getUpdateRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' =>'You must add at least 1 image.']);

            $categories = implode(',',Binput::get('categories', []));

            $this->dispatchNow(new UpdateInventoryItemCommand(
                $this->getUser(),
                $item,
                (int) Binput::get('style_id'),
                (int) Binput::get('size_id'),
                (float) Binput::get('price_usd'),
                (float) Binput::get('wholesale_price_usd', 0),
                (int) Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('description'),
                $categories,
                Binput::get('uuid')
            ));

            return $this->setData(['msg' => "Item {$item->name} updated", 'item' => $item->toJson()])->respond();
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
     *
     * @return \Illuminate\Http\Response
     */
    public function associate(Request $request)
    {
        $listingType = Binput::get('listingtype');
        $flashsaleId = Binput::get('flashsale', null);
        $selectedItems = (array) Binput::get('items', []);

        // Facebook data
        $facebookAlbums = (array) Binput::get('fb_albums', []);
        $facebookGroup = Binput::get('fb_group', null);
        $facebookGroupId = $facebookGroup ? $facebookGroup['id'] : null;

        // Facebook sales options, are, optional :)
        $options = (array) Binput::get('options', []);

        // Date to list it and remove it
        $listAt = array_get($options, 'list_at', null);
        $removeAt = array_get($options,'remove_at', null);
        // Date range you can claim.
        $claimableAt = array_get($options, 'available_at', null);
        $claimableUntil = array_get($options, 'available_until', null);
        $itemMessage = array_get($options, 'item_message', false);

        try {
            // You must have either a flashsaleid or facebookalbum
            if (($listingType == 'flashsale' && ! $flashsaleId ) || ($listingType == 'facebook' && count($facebookAlbums) == 0)) {
                throw new MissingMandatoryParametersException('You must select a sale');
            }

            if (count($selectedItems) == 0) {
                throw new MissingMandatoryParametersException;
            }

            if ($claimableAt && strtotime($claimableAt) < strtotime($listAt)) {
                throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
            }

            $listingOptions = new FacebookListingOptions($listAt, $removeAt, $claimableAt, $claimableUntil, $itemMessage);

            if ($listingType == 'flashsale' && $flashsaleId) {
                $command = new ScheduleFlashsaleListingcommand($this->getUser(), $flashsaleId, $selectedItems);
            } else {
                $command = new ScheduleFacebookListingCommand(
                    $this->getUser(),
                    $facebookAlbums,
                    $facebookGroupId,
                    $selectedItems,
                    $listingOptions
                );
            }

            $this->dispatchNow($command);

            return $this->setData(['msg' =>'Items scheduled successfully to sale.'])->respond();
        } catch (FacebookAuthenticationException $e) {
            $msg = 'Your facebook credentials are invalid. Please re-authorize '.env('APP_NAME').' for your facebook account, via our settings page.';
            return $this->setData(['msg' => $msg])->setStatusCode(500)->respond();
        }catch (MissingMandatoryParametersException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage() ? : 'You must select as least 1 item for listing.'])->respond();
        } catch (ListingConflictsWithExistingListingException $e) {
            return $this->setStatusCode(500)->setData(['msg' => 'The date and time block you selected conflicts with an existing listing. Please select a new block of time.'])->respond();
        } catch (Exception $e){
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $flashSaleItemId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAssociation(Request $request, $username, $flashSaleItemId)
    {
        $this->dispatchNow(new DeleteInventoryFromSaleCommand($this->getUser(), $flashSaleItemId));

        return $this->noContent();
    }
}