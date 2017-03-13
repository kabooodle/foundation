<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\FlashSales;

use Binput;
use Kabooodle\Http\Requests\Comments\CommentRequest;
use Messages;
use Response;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\FlashsaleItems;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\Claim\ClaimListedItemCommand;
use Kabooodle\Http\Controllers\Traits\CommentableControllerTrait;

/**
 * Class SellersInventoryController
 * @package Kabooodle\Http\Controllers\Web\FlashSales
 */
class SellersInventoryController extends Controller
{
    use CommentableControllerTrait, ObfuscatesIdTrait;

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
//            $data = $this->dispatchNow(new GetSellerInventoryCommand($item, webUser()));
//            return $this->view('flashsales.shop.index')->with(compact('data', 'item'));
//        } catch (GetSellerInventoryException $e) {
//            dd($e);
//        }

//        $listedItem = $item->listedItems;
//
//        return $this->view('flashsales.shop.index', [$saleIdAndName])->with(compact('item', 'inventory'));
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
        $shoppable = FlashsaleItems::where('flashsale_id', $decryptedId)
            ->where('inventory_id', $this->obfuscateFromURIString($itemIdAndName))
            ->first();
        $listedItem = $shoppable->listedItem;
        $item = $shoppable->flashsale;
        if ($item) {
            return $this->view('flashsales.shop.show')->with(compact('shoppable', 'inventory', 'item'));
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
     * @param         $saleIdAndName
     * @param         $itemIdAndName
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function claim(Request $request, $saleIdAndName, $itemIdAndName)
    {
        $decryptedId = $this->obfuscateFromURIString(Binput::clean($saleIdAndName));
        $flashSale = FlashSales::find($decryptedId);

        $listingItem = $flashSale->listingItems->find($this->obfuscateFromURIString(Binput::clean($itemIdAndName)));

        try {
            $this->dispatchNow(new ClaimListedItemCommand(webUser(), $listingItem, $listingItem->listedItem));

            Messages::success('Item claimed successfully!');
            return Response::json([], 200);
        } catch (Exception $e) {
            return \Response::json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param CommentRequest $request
     * @param                $saleIdAndName
     * @param                $itemIdAndName
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment(CommentRequest $request, $saleIdAndName, $itemIdAndName)
    {
        $decryptedId = $this->obfuscateFromURIString(Binput::clean($saleIdAndName));
        $flashSale = FlashSales::find($decryptedId);
        $listedItem = $flashSale->listedItems->find($this->obfuscateFromURIString(Binput::clean($itemIdAndName)));

        $data = self::handleStoreComment($listedItem, $request->getCommentText());

        return Response::json($data, 200);
    }

    /**
     * @param CommentRequest $request
     * @param                $saleIdAndName
     * @param                $itemIdAndName
     * @param                $commentId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment(CommentRequest $request, $saleIdAndName, $itemIdAndName, $commentId)
    {
        try {
            $decryptedId = $this->obfuscateFromURIString(Binput::clean($saleIdAndName));
            $flashSale = FlashSales::find($decryptedId);
            $listedItem = $flashSale->listedItems->find($this->obfuscateFromURIString(Binput::clean($itemIdAndName)));

            $comments = $listedItem->comments;
            $comment = $comments->find($commentId)->firstOrFail();

            $data = self::handleDeleteComment($listedItem, $comment);

            return Response::json($data, 200);
        } catch (Exception $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }
    }
}
