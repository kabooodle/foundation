<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
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
                'total' => $items->count()
            ];
            if ($items->count() > 0) {
                foreach($items as $item) {
                    if(! $groupings[$styleId]['name']) {
                        $groupings[$styleId]['name'] = $item->style->name;
                    }
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['id'] = str_random(16);
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['name'] = $item->styleSize->name;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['sizes'][$item->styleSize->id]['total_qty'] : $item->initial_qty;
                    $groupings[$styleId]['sizes'][$item->styleSize->id]['items'][] = [
                        'id' => $item->id,
                        'uuid' => $item->getUUID(),
                        'size_id' => $item->styleSize->id,
                        'size_name' => $item->styleSize->name,
                        'style_id' => $styleId,
                        'style_name' => $item->style->name,
                        'images' => $item->files->toArray(),
                        'initial_qty' => $item->initial_qty,
                        'price_usd' => $item->price_usd
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