<?php

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Categories;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ManageInventoryForSalesController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class ManageInventoryForSalesController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
//        $selectedItems = Binput::get('selected_items', []);

        $data = Inventory::all();
        return $this->view('inventory.associate.create')->with(compact('data'));
    }
}
