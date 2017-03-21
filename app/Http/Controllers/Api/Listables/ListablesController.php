<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listables;

use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listables\ArchiveListableCommand;
use Kabooodle\Bus\Commands\Listables\ActivateListableCommand;
use Kabooodle\Foundation\Exceptions\Listables\ItemNotArchiveableBelongsToOutfitsException;

/**
 * Class ListablesController
 * @package Kabooodle\Http\Controllers\Api\Listables
 */
class ListablesController extends AbstractApiController
{
    use PaginatesTrait;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Begin the user inventory query.
            $groupings = [];
            $inventory = Inventory::noEagerLoads()->active()->with(['claims', 'style', 'styleSize', 'files'])
                ->where('user_id', '=', $this->getUser()->id)->get();
            $grouped = $inventory->groupBy('inventory_type_styles_id');
            foreach($grouped as $styleId => $items) {
                $groupings[$styleId] = [
                    'name' => null,
                    'total' => $items->sum('initial_qty'),
                    'id' => $styleId,
                ];
                if ($items->count() > 0) {
                    foreach($items as $item) {
                        if(! $groupings[$styleId]['name']) {
                            $groupings[$styleId]['name'] = $item->style->name;
                        }
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['id'] = $item->styleSize->id;
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['name'] = $item->styleSize->name;
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] + $item->initial_qty : $item->initial_qty;
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['listables'][] = [
                            'id' => $item->id,
                            'name_uuid' => $item->name_uuid,
                            'uuid' => $item->uuid,
                            'name' => $item->name_with_variant,
                            'name_alt' => $item->name,
                            'initial_qty' => $item->initial_qty,
                            'available_qty' => $item->available_quantity,
                            'price_usd' => $item->price_usd,
                            'wholesale_price_usd' => $item->wholesale_price_usd,
                            'cover_photo' => $item->cover_photo->location,
                            'hash_id' => $item->hash_id,
                        ];
                    }

                    // Sort based on the order key.
                    usort($groupings[$styleId]['subgroupings'], function ($item1, $item2) {
                        return $item1['order'] <=> $item2['order'];
                    });
                }
            }

            $outfits = InventoryGrouping::active()->whereUserId($this->getUser()->id)->with(['claims'])->orderBy('name')->get();

            if ($outfits->count() > 0) {
                $id = $outfits->count() + 1;
                $groupings[$id] = [
                    'name' => 'Outfits',
                    'total' => $outfits->sum('initial_qty'),
                    'id' => $id,
                ];
                foreach ($outfits as $item) {
                    $groupings[$id]['subgroupings'][$item->id]['id'] =$item->id;
                    $groupings[$id]['subgroupings'][$item->id]['order'] = 0;
                    $groupings[$id]['subgroupings'][$item->id]['name'] = $item->name;
                    $groupings[$id]['subgroupings'][$item->id]['total_qty'] = $item->available_quantity;
                    $groupings[$id]['subgroupings'][$item->id]['listables'][] = [
                        'id' => $item->id,
                        'name_uuid' => $item->name_uuid,
                        'uuid' => $item->uuid,
                        'name' => $item->name_with_variant,
                        'name_alt' => $item->name,
                        'initial_qty' => $item->initial_qty,
                        'available_qty' => $item->available_quantity,
                        'price_usd' => $item->price_usd,
                        'wholesale_price_usd' => $item->wholesale_price_usd,
                        'cover_photo' => $item->cover_photo->location,
                        'hash_id' => $item->hash_id,
                    ];
                }
            }

            sort($groupings);

            return $this->setData($groupings)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, $id)
    {
        try {
            $listable = $this->getUser()->listables()->findOrFail($id);

            $this->dispatchNow(new ArchiveListableCommand(
                $listable,
                $this->getUser()
            ));

            return $this->setData([
                'msg' => "Item archived",
            ])->respond();
        } catch (ItemNotArchiveableBelongsToOutfitsException  $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => 'Item cannot be archived as it is currently associated to an outfit'])
                ->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request, $id)
    {
        try {
            $listable = $this->getUser()->listables()->findOrFail($id);

            $this->dispatchNow(new ActivateListableCommand(
                $listable,
                $this->getUser()
            ));

            return $this->setData([
                'msg' => "Item unarchived",
            ])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }
}
