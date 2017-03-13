<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Datatables;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Claim\ClaimInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class InventoryController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryArchiveController extends Controller
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

        return $this->view('inventory.archive');
    }
}
