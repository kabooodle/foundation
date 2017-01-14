<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\FlashSales;

use DB;
use Binput;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Requests\Flashsale\FlashsaleViewRequest;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Messages;

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
        $data = FlashSales::withoutExpired()->paginate();

        return $this->view('flashsales.index')->with(compact('data'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, FlashSales::getRules());

            $startsEnds = new StartsAndEndsAt(
                strtotime(Binput::get('starts_at')),
                strtotime(Binput::get('ends_at'))
            );

            $flashsale = $this->dispatchNow(new AddFlashsaleCommand(
                user(),
                Binput::get('name'),
                Binput::get('description'),
                $startsEnds,
                Binput::get('type', FlashSales::HOST_SELF),
                Binput::get('group_id', null),
                Binput::get('rules')
            ));

            Messages::success("The flash sale, {$flashsale->name}, was successfully created!");

            return $this->redirect(route('flashsales.index'));
        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('flashsales.create'))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @param FlashsaleViewRequest $request
     * @param                      $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(FlashsaleViewRequest $request, $idAndName)
    {
        $item = $request->getFlashsale();

        if ($item && ! $item->saleHasEnded()) {
            return $this->view('flashsales.show')->with(compact('item'));
        }

        return $this->redirect('/');
    }

    /**
     * @param FlashsaleViewRequest $request
     * @param                      $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(FlashsaleViewRequest $request, $idAndName)
    {
        $item = $request->getFlashsale();

        if ($item) {
            if (!$item->owner->id == user()->id) {
                return redirect()->route('flashsales.show', [$idAndName]);
            }

            return $this->view('flashsales.edit')->with(compact('item'));
        }

        return $this->redirect(route('shop.index'));
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

            if (!$item->owner->id == user()->id) {
                return redirect()->route('flashsales.show', [$idAndName]);
            }

            $startsEnds = new StartsAndEndsAt(
                strtotime(Binput::get('starts_at')),
                strtotime(Binput::get('ends_at'))
            );

            $item = $this->dispatchNow(new UpdateFlashsaleCommand(
                $item,
                user(),
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
