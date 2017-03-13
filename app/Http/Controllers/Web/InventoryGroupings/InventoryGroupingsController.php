<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\InventoryGroupings;

use Dingo\Api\Routing\Helpers;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Models\InventoryGrouping;
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
 * Class InventoryGroupingsController
 * @package Kabooodle\Http\Controllers\Web\InventoryGroupings
 */
class InventoryGroupingsController extends Controller
{
    use ObfuscatesIdTrait, PaginatesTrait, Helpers;

    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function index(Request $request, $username)
    {
        return $this->view('inventory-groupings.index');
    }

    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function simple(Request $request, $username)
    {
        return $this->view('inventory-groupings.simple');
    }

    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function create(Request $request, $username)
    {
        if (webUser()->username <> $username) {
            return redirect('/');
        }

        return $this->view('inventory-groupings.form');
    }

    /**
     * @param Request $request
     * @param $username
     * @param $idAndName
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function show(Request $request, $username, $idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);

        $grouping = InventoryGrouping::with(['comments','sales', 'views'])->findOrFail($decryptedId);

        if ($grouping) {
            $data = [
                'listable' => $grouping,
            ];

            return $this->view('listables.show', $data);
        }

        return $this->redirect(route('shop.outfits.index', [$username]));
    }

    /**
     * @param Request $request
     * @param $username
     * @param $idAndName
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function edit(Request $request, $username, $idAndName)
    {
        if (webUser()->username <> $username) {
            return redirect('/');
        }

        $decryptedId = $this->obfuscateFromURIString($idAndName);

        $grouping = InventoryGrouping::with('inventoryItems')->findOrFail($decryptedId);

        if ($grouping) {
            $data = [
                'grouping' => $grouping->toJson(),
            ];

            return $this->view('inventory-groupings.form', $data);
        }

        return $this->redirect(route('shop.outfits.index', [$username]));
    }
}
