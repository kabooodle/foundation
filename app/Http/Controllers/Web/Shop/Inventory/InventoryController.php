<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

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
    use ObfuscatesIdTrait;

    /**
     * @param Request $request
     * @param         $username
     *
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function index(Request $request, $username)
    {
        if (user()->username <> $username) {
            return redirect('/');
        }

        $data = user()->inventory;

//        if ($request->has('style_id') && $request->get('style_id')) {
//            $data = $data->whereInLoose('inventory_type_styles_id', $request->get('style_id'));
//        }
//        if ($request->has('size_id') && $request->get('size_id')) {
//            $data = $data->whereInLoose('inventory_sizes_id', $request->get('size_id'));
//        }
//        if ($request->has('qty_0')) {
//            $data = $data->where('initial_qty', 0);
//        }
//        if ($request->has('flashsale_id')) {
//            $data = $data->whereInLoose('flashsales', $request->get('flashsale_id'));
//        }
//        if ($request->has('has_sales')) {
//            $data = $data->filter(function($item){
//                return $item->sales->count() > 0;
//            });
//        }
//        if ($request->has('has_claims')) {
//            $data = $data->filter(function($item){
//                return $item->claims->count() > 0;
//            });
//        }

        $page = $request->get('page', 1);
        $perPage = config('pagination.per-page');

        $data = new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            count($data),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        if ($request->wantsJson()) {
            return Response::json(fractal()
                ->collection($data)
                ->transformWith(new InventoryTransformer())
                ->paginateWith(new IlluminatePaginatorAdapter($data))
                ->toArray());
        }

        // Base filters.
        $filters = [
            'styles' => [],
            'sizes' => [],
            'flashSales' => [],
            'claims' => [],
            'approvedsales'  => []
        ];

        // Build arrays of filterable data.
        // We only want the user to have filters that are relevant to their inventory.
        foreach($data as $item) {
            // we need all styles
            $filters['styles'][] = $item->style;
            // we need all sizes
            $filters['sizes'][] = $item->style->sizes->find($item->inventory_sizes_id);
            // we need flashsales
            if ($item->flashsales->count() > 0) {
                $filters['flashSales'][] = $item->flashsales;
            }
        }

        $filters['styles'] = collect($filters['styles'])->unique();
        $filters['sizes'] = collect($filters['sizes'])->unique();
        $filters['flashSales'] = collect($filters['flashSales'])->unique()->filter(function($sale){
            return $sale->saleHasEnded() ? false : true;
        });
        $filters['claims'] = collect($filters['claims'])->unique()->filter(function($claim){
            return $claim->wasRejected() ? false : true;
        });

        return $this->view('inventory.index')->with(compact('data', 'filters'));
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
