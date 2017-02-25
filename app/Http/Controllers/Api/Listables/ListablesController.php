<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listables;

use DB;
use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Illuminate\Validation\ValidationException;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingCommand;
use Kabooodle\Transformers\Inventory\InventoryTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

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
        // Begin the user inventory query.
        $groupings = [];
        $inventory = $this->getUser()->inventory()->NoEagerLoads()->with(['style', 'styleSize', 'files'])->get();
        $grouped = $inventory->groupBy('inventory_type_styles_id');
        foreach($grouped as $styleId => $items) {
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
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['id'] = $item->styleSize->id;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['name'] = $item->styleSize->name;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] : $item->initial_qty;
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
                        'cover_photo' => $item->cover_photo,
                        'hash_id' => $item->hash_id,
                    ];
                }

                // Sort based on the order key.
                usort($groupings[$styleId]['subgroupings'], function ($item1, $item2) {
                    return $item1['order'] <=> $item2['order'];
                });
            }
        }

        $outfits = InventoryGrouping::whereUserId($this->getUser()->id)->orderBy('name')->get();

        if ($outfits->count() > 0) {
            $id = $outfits->count() + 1;
            $groupings[$id] = [
                'name' => 'Outfits',
                'total' => $outfits->count(),
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
                    'name' => $item->name,
                    'name_alt' => 'outfits',
                    'uuid' => $item->uuid,
                    'initial_qty' => $item->initial_qty,
                    'available_qty' => $item->available_quantity,
                    'price_usd' => $item->price_usd,
                    'wholesale_price_usd' => $item->wholesale_price_usd,
                    'cover_photo' => $item->cover_photo,
                    'hash_id' => $item->hash_id,
                ];
            }
        }

        sort($groupings);

        $data = [
            'inventory' => $inventory,
            'groupings' => $groupings,
        ];

        return $this->setData($data)->respond();
    }
}