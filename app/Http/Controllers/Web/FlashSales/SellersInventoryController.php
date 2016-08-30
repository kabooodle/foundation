<?php

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
     * @return $this
     */
    public function index($saleIdAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($saleIdAndName);
        $item = FlashSales::find($decryptedId);

        try {
            $items = $this->dispatchNow(new GetSellerInventoryCommand($item, user()));
            return $items->load(['item', 'seller']);
        } catch (GetSellerInventoryException $e) {
            dd($e);
        }
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
     * @param $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($saleIdAndName, $username)
    {
        $decryptedId = $this->obfuscateFromURIString($saleIdAndName);
        $item = FlashSales::find($decryptedId);

        if ($item && $seller = $item->sellers->filter(function($user) use ($username){
                return $user->username == $username;
            })->first()) {
            dd($seller);
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
