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
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;
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
                        'files' => $item->files
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
            $item = user()->inventory()->findOrFail($id);
            $this->validate($request, Inventory::getUpdateRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' =>'You must add at least 1 image.']);

            $this->dispatchNow(new UpdateInventoryItemCommand(
                user(),
                $item,
                (int) Binput::get('style_id'),
                (int) Binput::get('size_id'),
                (float) Binput::get('price_usd'),
                (float) Binput::get('wholesale_price_usd', 0),
                (int) Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('description'),
                Binput::get('categories'),
                Binput::get('uuid')
            ));

            return $this->setData(['msg' => "Item {$item->name} updated", 'item' => $item->toJson()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An unknown error occurred, please try again.'])
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
        $flashsaleId = Binput::get('flashsales', null);
        $selectedItems = (array) Binput::get('items', []);

        // Facebook data
        $facebookAlbums = (array) Binput::get('fb_albums', []);
        $facebookGroup = Binput::get('fb_group', null);
        $facebookGroupId = $facebookGroup ? $facebookGroup['id'] : null;

        // Facebook sales options
        $options = (array) Binput::get('options', []);
        $endsAt = array_get($options, 'ends_at', null);
        $includeText = (bool) array_get($options, 'include_text', false);
        $claimableAt = array_get($options, 'available_at', null);

        try {
            // You must have either a flashsaleid or facebookalbum
            if (! $flashsaleId && count($facebookAlbums) == 0) {
                throw new MissingMandatoryParametersException;
            }

            if ($flashsaleId && count($selectedItems) == 0) {
                throw new MissingMandatoryParametersException;
            }

            if ($claimableAt && $includeText && (strtotime($claimableAt) < strtotime($endsAt))) {
                throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
            }

            $command = new ScheduleListingCommand(
                $this->getUser(),
                $includeText,
                $endsAt,
                $claimableAt,
                $flashsaleId,
                $facebookAlbums,
                $facebookGroupId,
                $selectedItems
            );

            $this->dispatchNow($command);

            return $this->setData(['msg' =>'Items scheduled successfully for queuing.'])->respond();
        } catch (FacebookAuthenticationException $e) {
            $msg = 'Your facebook credentials are invalid. Please re-authorize '.env('APP_NAME').' for your facebook account, via our settings page.';
            return $this->setData(['msg' => $msg])->$this->setStatusCode(500)->respond();
        }catch (MissingMandatoryParametersException $e) {
            return $this->setStatusCode(500)->setData(['msg' => 'You must select as least 1 item for listing.'])->respond();
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