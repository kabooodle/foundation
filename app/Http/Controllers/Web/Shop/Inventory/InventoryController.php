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
 * Class InventoryController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $data = user()->inventory;

        $page = $request->get('page', 1);
        $perPage = config('pagination.per-page');

        $data = new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            count($data),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->view('inventory.index')->with(compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->view('inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $username)
    {
        try {
            $this->validate($request, Inventory::getRules());

            $item = Inventory::factory([
                'name' => Binput::get('name'),
                'description' => Binput::get('description'),
                'current_qty' => (int) Binput::get('current_qty'),
                'user_id' => user()->id,
                'price_usd' => Binput::get('price_usd')
            ]);

            $requestCategories = Binput::get('categories');
            if ($requestCategories) {
                $categories = Categories::whereIn('id', [$requestCategories])->get();
                $item->categories()->saveMany($categories);
            }

            $tags = Binput::get('tags');
            if ($tags) {
                $item->tag($tags);
            }

            if (Binput::get('flashsales', false) && $item->current_qty > 0) {
                $this->dispatchNow(new AddInventoryToSalesCommand(user(), [$item->id], Binput::get('flashsales')));
            }

            event(new InventoryItemWasAddedEvent($item));

            Messages::success("New item, {$item->name}, was successfully added to your inventory!");

            return $this->redirect(route('shop.inventory.index', [$username]));

        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('shop.inventory.create', [$username]))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show($username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
//        $item = user()->inventory->find($decryptedId);
        $item = Inventory::find($decryptedId);

        if ($item) {
            return $this->view('inventory.show')->with(compact('item'));
        }

        return $this->redirect(route('shop.inventory.index', [$username]));
    }

    /**
     * @param $username
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = user()->inventory->find($decryptedId);

        if ($item) {
            return $this->view('inventory.edit')->with(compact('item'));
        }

        return $this->redirect(route('shop.inventory.index', [$username]));
    }

    /**
     * @param Request $request
     * @param         $idAndName
     *
     * @return $this
     */
    public function update(Request $request, $username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = user()->inventory->find($decryptedId);

        try {
            $this->validate($request, Inventory::getRules());

            $this->dispatchNow(new UpdateInventoryItemCommand($item, Binput::all()));

            Messages::success("Item {$item->name} updated");

            return redirect()->route('shop.inventory.show', [$username, $idAndName]);

        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('shop.inventory.create', [$username]))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
