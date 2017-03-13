<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\FlashSales;

use Binput;
use Kabooodle\Models\Listings;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Requests\Flashsale\FlashsaleViewRequest;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;

/**
 * Class FlashSalesController
 * @package Kabooodle\Http\Controllers\Web\FlashSales
 */
class FlashSalesController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('flashsales.index');
    }

    /**
     * @param FlashsaleViewRequest $request
     * @param                      $idAndName
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(FlashsaleViewRequest $request, $idAndName)
    {
        $flashsale = $request->getFlashsale(['listing']);

        $rawCategories = collect(Listings::getStyleGroupingsForFlashsale($flashsale->id));

        $categories = $rawCategories->sortBy('style_name')->groupBy('style_name')->transform(function($item, $k){
            return $item->groupBy('size_name');
        });

        $sellersCategories = collect(Listings::getSellersForFlashsale($flashsale->id));

        return $this->view('flashsales.show')->with(compact('flashsale', 'categories', 'sellersCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->view('flashsales.create');
    }

    /**
     * @param FlashsaleViewRequest $request
     * @param                      $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(FlashsaleViewRequest $request, $idAndName)
    {
        $flashsale = $request->getFlashsale();

        if ($flashsale) {
            if (!$flashsale->canSellerListToFlashsaleAnytime(webUser()->id)) {
                return redirect()->route('flashsales.show', [$idAndName]);
            }

            return $this->view('flashsales.edit')->with(compact('flashsale'));
        }

        return $this->redirect()->route('flashsales.index');
    }

    /**
     * @param FlashsaleViewRequest $request
     * @param                      $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(FlashsaleViewRequest $request, $idAndName)
    {
        try {
            $this->validate($request, FlashSales::getRules());
            $item = $request->getFlashsale();

            if (!$item->owner->id == webUser()->id) {
                return redirect()->route('flashsales.show', [$idAndName]);
            }

            $startsEnds = new StartsAndEndsAt(Binput::get('starts_at'), Binput::get('ends_at'));

            $item = $this->dispatchNow(new UpdateFlashsaleCommand(
                $item,
                webUser(),
                Binput::get('name'),
                Binput::get('description'),
                $startsEnds,
                Binput::get('seller_rules'),
                Binput::get('admins', []),
                explode(',', Binput::get('sellers_invites', [])),
                Binput::get('privacy')
            ));

            Messages::success("{$item->name}, was successfully updated!");

            return $this->redirect()->route('flashsales.show', [$idAndName]);
        } catch (ValidationException $e) {
            Messages::error('Some fields require attention!');

            return $this->redirect(route('flashsales.edit', [$item->getUUID()]))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
