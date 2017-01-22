<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Binput;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Models\Listings;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ListingsApiController
 */
class ListingsApiController extends AbstractApiController
{
    use PaginatesTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = $this->getUser()->listings;

        return $this->setData($listings)->respond();
    }

    /**
     * @param Request $request
     * @param string  $listingUuid
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $listingUuid)
    {
        try {
            $listing = Listings::with(['items', 'items.inventoryItem'])
                ->where('uuid', $listingUuid)
                ->orderBy('scheduled_for')
                ->first();

            if (! $listing) {
                throw new ModelNotFoundException;
            }

            $items = $listing->items;

            $style_query = Binput::get('styles');
            $size_query = Binput::get('sizes');
            $sellers_query = Binput::get('sellers');

            if ($style_query ) {
                $items = $items->filter(function($item) use ($style_query){
                    return in_array($item->inventoryItem->inventory_type_styles_id, $style_query);
                });
            }

            if ($size_query ) {
                $items = $items->filter(function($item) use ($size_query){
                    return in_array($item->inventoryItem->inventory_sizes_id, $size_query);
                });
            }

            if ($sellers_query) {
                $items = $items->filter(function($item) use ($sellers_query){
                    return in_array($item->owner_id, $sellers_query);
                });
            }

            $items = $items->sortBy(function($item){
                return $item->make_available_at;
            })->sortBy(function($item){
                return $item->id;
            })->values();

            $items = $this->paginateData($request, $items);

            return $this->setData($items)->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}
