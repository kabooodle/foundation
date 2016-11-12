<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Sales;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;

/**
 * Class SalesController
 * @package Kabooodle\Http\Controllers\Web\Sales
 */
class SalesController extends Controller
{
    use PaginatesTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $sales = user()->acceptedClaimsOnMyInventory;
        $sales = $this->paginateData($request, $sales);

        return $this->view('sales.index', compact('sales'));
    }
}