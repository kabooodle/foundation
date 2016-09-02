<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\FlashSales;

use Binput;
use Kabooodle\Bus\Commands\Flashsale\Inventory\GetSellerInventoryCommand;
use Kabooodle\Foundation\Exceptions\GetSellerInventoryException;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class SellersInventoryController
 * @package Kabooodle\Http\Controllers\Web\FlashSales
 */
class SellersInventoryController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * This is where we will list all the sellers' items.
     *
     * We are not yet sure how we are going to categorize/group items together because we currently
     * dont have any sort of category system for inventory items... We could just list it according to
     * timestamps they were added into the sale, which would create a FOMO for those who aren't active.
     * We could also let the admins of the sale determine the manner in which items are to be displayed.
     *
     * @param $saleIdAndName
     *
     */
    public function index($saleIdAndName)
    {
        return redirect()->route('flashsales.show', [$saleIdAndName]);


//        $decryptedId = $this->obfuscateFromURIString($saleIdAndName);
//        $item = FlashSales::find($decryptedId);

//        try {
//            $data = $this->dispatchNow(new GetSellerInventoryCommand($item, user()));
//            return $this->view('flashsales.shop.index')->with(compact('data', 'item'));
//        } catch (GetSellerInventoryException $e) {
//            dd($e);
//        }

//        $inventory = $item->inventoryItems;
//
//        return $this->view('flashsales.shop.index', [$saleIdAndName])->with(compact('item', 'inventory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // What would this resource method be for? Not sure.
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // What would this resource method be for? Not sure.
    }

    /**
     * @param $saleIdAndName
     * @param $itemIdAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show($saleIdAndName, $itemIdAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($saleIdAndName);
        $item = FlashSales::find($decryptedId);

        $inventory = $item->inventoryItems->find($this->obfuscateFromURIString($itemIdAndName));
        if ($item) {
            return $this->view('flashsales.shop.show')->with(compact('inventory', 'item'));
        }

        return redirect()->route('flashsales.shop.index', [$saleIdAndName]);
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($idAndName, $username)
    {
       //
    }

    /**
     * @param Request $request
     * @param         $idAndName
     */
    public function update(Request $request, $idAndName, $username)
    {
       //
    }

    /**
     * @param Request $request
     * @param         $idAndName
     * @param         $username
     */
    public function destroy(Request $request, $idAndName, $username)
    {
        // I'm not sure this should be used unless you are an admin.
    }
}
