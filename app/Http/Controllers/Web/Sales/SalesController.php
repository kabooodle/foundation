<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Sales;

use Binput;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;

/**
 * Class SalesController
 * @package Kabooodle\Http\Controllers\Web\Sales
 */
class SalesController extends Controller
{
    use PaginatesTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $filters = [
            'style' =>  Binput::get('style', []),
            'size' =>   Binput::get('size', []),
            'statii' => Binput::get('statii', []),
            'price_range' => Binput::get('price_range', false),
            'categories' => Binput::get('categories', []),
            'startdate' => Binput::get('startdate', false),
            'enddate' => Binput::get('enddate', false),
            'purchasers' => $request->has('purchasers') ? array_filter(Binput::get('purchasers')) : false
        ];

        $sales = user()->acceptedClaimsOnMyInventory()->noEagerLoads()->with(['inventoryItem']);


        // Price filter
        if ($filters['price_range']) {
            $sales = $sales->whereRaw('coalesce(accepted_price, price) >= 0')->whereRaw('coalesce(accepted_price, price) <= ?', [$filters['price_range']]);
        }

        // Styles, Size, Price filters
        if (count($filters['style'])>0 || count($filters['size'])>0) {
            $sales = $sales->whereHas('inventoryItem', function ($q) use ($filters) {
                // Styles
                if (count($filters['style'])>0) {
                    $q->whereIn('inventory_type_styles_id', $filters['style']);
                }
                // Sizes
                if (count($filters['size'])>0) {
                    $q->whereIn('inventory_sizes_id', $filters['size']);
                }
            });
        }

        // Recipients filter
        if ($filters['purchasers']) {
            $sales = $sales->whereIn('claimed_by',  $filters['purchasers']);
        }

        // Date filter
        if ($filters['startdate'] && $filters['enddate']) {
            $startDate = Carbon::createFromTimestamp(strtotime($filters['startdate'].' 00:00:00'))->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromTimestamp(strtotime($filters['enddate'].' 23:59:59'))->format('Y-m-d H:i:s');
            $sales = $sales->whereBetween('accepted_on', [$startDate, $endDate]);
        }

        // Statii filter
        if (count($filters['statii'])>0) {
            $statii = $filters['statii'];

            // If its not in this array, we are dealing with claims that have a kabooodle shipping transaction
            if (!in_array('PENDING LABEL CREATION', $statii) && !in_array('EXTERNALLY SHIPPED', $statii)) {
                $sales = $sales->whereHas('shipments.transaction', function ($q) use ($statii) {
                    $q->whereIn('shipping_status', $statii);
                });
            } else {

                // We want externally shipped claims.
                if (in_array('EXTERNALLY SHIPPED', $statii)) {
                    $sales = $sales->where('shipped_manually', '=', 1);
                }

                // We want claims in the shipping queue
                if (in_array('PENDING LABEL CREATION', $statii)) {
                    $sales = $sales->has('shippingQueue');
                }
            }
        }

        $sales = $this->paginateData($request, $sales->get());

        return $this->view('sales.index', compact('sales', 'filters'));
    }
}
