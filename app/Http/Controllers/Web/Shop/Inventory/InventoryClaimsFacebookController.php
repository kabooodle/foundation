<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Response;
use Exception;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\FacebookItems;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class InventoryClaimsFacebookController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryClaimsFacebookController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param Request $request
     * @param         $facebookItemString
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Contracts\View\View
     */
   public function show(Request $request, $facebookItemString)
   {
       $facebookItemId = $this->obfuscateStringToId($facebookItemString);
       $facebookItem = FacebookItems::find($facebookItemId);
       if ($facebookItem && $facebookItem->inventory) {
           return $this->view('shop.facebookclaim')->with(['item' => $facebookItem->inventory, 'facebook' => $facebookItem]);
       }

       return abort(404);
   }

    /**
     * @param $facebookItemString
     *
     * @return \Illuminate\Http\JsonResponse
     */
   public function claim($facebookItemString)
   {
       $facebookItemId = $this->obfuscateStringToId($facebookItemString);
       $facebookItem = FacebookItems::find($facebookItemId);

       try {
           $this->dispatchNow(new ClaimInventoryItemCommand(webUser(), $facebookItem, $facebookItem->inventory));

           Messages::success('Item claimed successfully!');
           return Response::json([], 200);
       } catch (Exception $e) {
           return \Response::json(['message' => $e->getMessage()], 500);
       }
   }
}
