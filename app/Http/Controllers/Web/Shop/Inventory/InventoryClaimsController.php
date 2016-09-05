<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Illuminate\Pagination\LengthAwarePaginator;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Categories;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;

/**
 * Class InventoryClaimsController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryClaimsController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, $username)
    {
        $data = user()->claimsOnMyInventory;

        $page = $request->get('page', 1);
        $perPage = config('pagination.per-page');

        $data = new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            count($data),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->view('inventory.claims.index')->with(compact('data'));
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show($username, $idAndName)
    {
        //
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $claimsUUID
     */
    public function update(Request $request, $username, $claimsUUID)
    {
        $data = user()->claimsOnMyInventory;
        $item = $data->filter(function($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $claimsUUID
     */
    public function destroy(Request $request, $username, $claimsUUID)
    {
        $data = user()->claimsOnMyInventory;
        $item = $data->filter(function($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();
    }
}
