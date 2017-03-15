<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Kabooodle\Bus\Commands\Claim\ClaimListedItemCommand;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Messages;
use Response;
use Binput;
use Datatables;
use Illuminate\Routing\Redirector;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Commands\Inventory\GetInventoryTypesCommand;
use Kabooodle\Models\InventoryType;
use Kabooodle\Transformers\Inventory\InventoryTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Models\User;

/**
 * Class InventoryController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryController extends Controller
{
    use ObfuscatesIdTrait, PaginatesTrait;

    /**
     * @param Request $request
     * @param         $username
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function index(Request $request, $username)
    {
        if (webUser()->username <> $username) {
            return abort(404);
        }

        return $this->view('inventory.index');
    }

    /**
     * @param Request $request
     * @param         $username
     *
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function simple(Request $request, $username)
    {
        return $this->view('inventory.simple');
    }

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
            $this->validate($request, Inventory::getRules(), ['sizings.*.images.required' => 'You must add at least 1 image for each size.']);

            $command = new AddInventoryCommand(
                webUser(),
                Binput::get('type_id'),
                Binput::get('style_id'),
                Binput::get('price_usd'),
                Binput::get('wholesale_price_usd'),
                Binput::get('sizings'),
                Binput::get('description')
            );
            $items = $this->dispatchNow($command);

            Messages::success(count($items)." successfully added to your inventory!");

            return $this->redirect(route('shop.inventory.index', [$username]));
        } catch (ValidationException $e) {
            Messages::error('Some fields require input! '. $e->validator->messages()->first());

            return $this->redirect(route('shop.inventory.create', [$username]))
                ->withErrors($e->validator->getMessageBag())->withInput($request->all());
        }
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse|Redirector|string
     */
    public function show(Request $request, $username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = Inventory::with(['sales', 'views'])->findOrFail($decryptedId);

        if ($item) {
            $data = [
                'listable' => $item,
            ];
            if ($request->ajax()) {
                return $this->view('inventory.partials._show', $data)->render();
            }

            return $this->view('listables.show', $data);
        }

        return $this->redirect(route('shop.inventory.index', [$username]));
    }

    /**
     * @param $username
     * @param $idAndName
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request, $username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = webUser()->inventory->find($decryptedId);

        if ($item) {
            $styles = InventoryType::LuLaRoe()->first()->styles;
            if ($request->ajax()) {
                return $this->view('inventory.partials._edit')->with(compact('item', 'styles'));
            }

            return $this->view('inventory.edit')->with(compact('item', 'styles'));
        }

        return $this->redirect(route('shop.inventory.index', [$username]));
    }

    /**
     * @param Request $request
     * @param         $idAndName
     *
     * @return $this
     * @deprecated use API route instead.
     */
    public function update(Request $request, $username, $idAndName)
    {
        return redirect()->route('inventory.index');
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

        // We use the item so that we can store various meta data about it at the time of the claim.
        // This metadata might include price, size, name, etc;  The reason we store this data with the claim,
        // rather than just pivot to the item from the claim is because this data may change numerous time.
        // Storing this data allows us to preserve it at the time of the claim.
        // We remove the eager loaded relationships on inventory because most of it is unnecessary.
        $item = $user->inventory()->noEagerLoads()->with('style', 'size', 'styleSize', 'files')->find($decryptedId);
        try {
            $this->dispatchNow(new ClaimListedItemCommand(webUser(), $item));

            Messages::success('Item claimed successfully!');

            return Response::json([], 200);
        } catch (Exception $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postables(Request $request)
    {
        // Returns array
        $facebookGroups = webUser()->getFacebookGroups() ? : [];
        $flashSales = webUser()->currentFlashsalesAsSellerAndAdmins() ? : [];

        return Response::json(['data' => ['flashsales' => $flashSales, 'facebookgroups' => $facebookGroups]], 200);
    }
}
