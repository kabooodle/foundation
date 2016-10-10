<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Datatables;
use Illuminate\Routing\Redirector;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Commands\Inventory\GetInventoryTypesCommand;
use Kabooodle\Models\InventoryType;
use Messages;
use Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Categories;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Models\User;

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
    public function index(Request $request, $username)
    {
        if (user()->username <> $username) {
            return redirect('/');
        }

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

//    public function queryIndex()
//    {
//        return Datatables::collection(user()->inventory->load([
//            'categories',
//            'claims',
//            'tagged',
//            'acceptedClaims',
//            'pendingClaims'
//        ]))
//            ->addRowAttr('valign', 'middle')
//            ->addRowAttr('style',
//                'padding-bottom: 0; padding-top: 0; padding-left: 0; vertical-align: middle !important')
//            ->editColumn('id', ' <div class="list-item p-r-0 ">
//                        <input type="checkbox" class="selected_items_checkbox" name="selected_items[]"
//                               value="{{ $id }}">
//                    </div>', 0)
//            ->editColumn('name', '<div class="list ">
//                        <div class="list-item p-l-0 p-r-0">
//                            <div class="list-left">
//
//                        <span class="w-40 avatar">
//                                            <img src="https://placekitten.com/g/32/32">
//                                          </span>
//                            </div>
//                            <div class="list-body">
//                                <div>
//                                    <a href=""
//                                       class="_500 h6">{{ $name }}</a>
//                                </div>
//                                <div class="text-ellipsis text-muted text-sm">
//                                    Categories: @foreach($categories as $cat) {{ $cat["name"] }} @endforeach</div>
//                                <div class="text-sm hidden-sm hidden-xs hidden-xs-down">
//                                    @if(count($tagged) > 0)
//                                        <span class="text-muted">Tags: </span>
//                                        @foreach($tagged as $tag) <span
//                                                class="label label-xs outline text-u-c">{!! $tag["tag_name"] !!}</span> @endforeach
//                                    @endif
//                                </div>
//                            </div>
//                        </div>
//                    </div>', 1)
//            ->editColumn('price_usd', '<span class="h5">${{$price_usd}}</span>')
//            ->addColumn('claims',
//                ' <span class="h5 "><span class="text-success">{{ count($accepted_claims) }}</span> <span class="text-muted">|</span> <span class="text-warning">{{ count($pending_claims) }}</span> </span>')
//            ->editColumn('initial_qty', ' <span class="h5">{{ $initial_qty }}</span>')
//            ->make(true);
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventoryTypes = $this->dispatchNow(new GetInventoryTypesCommand(['lularoe']));

        return $this->view('inventory.create')->with(compact('inventoryTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|Redirector
     */
    public function store(Request $request, $username)
    {
        try {
            $this->validate($request, Inventory::getRules());

            $command = new AddInventoryCommand(
                user(),
                Binput::get('type_id'),
                Binput::get('style_id'),
                Binput::get('price_usd'),
                Binput::get('sizings'),
                Binput::get('description')
            );
            $items = $this->dispatchNow($command);

            Messages::success(count($items)." successfully added to your inventory!");

            return $this->redirect(route('shop.inventory.index', [$username]));

        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('shop.inventory.create', [$username]))
                ->withErrors($e->validator->getMessageBag())->withInput($request->all());
        }
    }

    /**
     * @param $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = user()->inventory->find($decryptedId);

        if ($item) {

            $styles = InventoryType::LuLaRoe()->first()->styles;

            return $this->view('inventory.edit')->with(compact('item', 'styles'));
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
            $this->validate($request, Inventory::getUpdateRules());

            $this->dispatchNow(new UpdateInventoryItemCommand(
                user(),
                $item,
                Binput::get('style_id'),
                Binput::get('size_id'),
                Binput::get('price_usd'),
                Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('description'),
                Binput::get('categories')
            ));

            Messages::success("Item {$item->name} updated");

            return redirect()->route('shop.inventory.show', [$username, $idAndName]);

        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('shop.inventory.edit', [$username, $idAndName]))
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

    /**
     * @param Request $request
     * @param         $username
     * @param         $idAndName
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function claim(Request $request, $username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString(Binput::clean($idAndName));
        $user = User::where('username', $username)->first();

        $item = $user->inventory->find($decryptedId);
        try {
            $this->dispatchNow(new ClaimInventoryItemCommand(user(), $user, $item));

            Messages::success('Item claimed successfully!');

            return Response::json([], 200);
        } catch (Exception $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }
    }
}
