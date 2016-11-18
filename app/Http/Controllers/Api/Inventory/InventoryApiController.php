<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;

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
            $this->validate($request, Inventory::getUpdateRules(), ['images.required' =>'You must add at least 1 image.']);

            $this->dispatchNow(new UpdateInventoryItemCommand(
                user(),
                $item,
                Binput::get('style_id'),
                Binput::get('size_id'),
                Binput::get('price_usd'),
                Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('description'),
                Binput::get('categories'),
                Binput::get('uuid')
            ));

            return $this->setData(['msg' => "Item {$item->name} updated", 'item' => $item->toJson()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input!: '.$e->validator->messages()->first()])
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
     * @return string
     */
    public function associate(Request $request)
    {
        $flashsaleIds = Binput::get('flashsalesales_ids', []);
        $facebookAlbumIds = Binput::get('fb_albums', []);
        $inventoryIds = Binput::get('selected_items_ids', []);

        $result = $this->dispatchNow(new AddInventoryToSalesCommand($this->getUser(), $inventoryIds, $flashsaleIds, $facebookAlbumIds));

        return $this->setData($result)->respond();
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