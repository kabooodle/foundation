<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Models\Claims;
use Kabooodle\Models\User;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;


/**
 * Class ProfileCreditCardController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfilePurchasesController extends Controller
{
    use PaginatesTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $claims = $this->paginateData($request, user()->claimsAsBuyer->sortByDesc('created_at'));
        return view('profile.purchases')->with(compact('claims'));
    }

    public function show($itemID)
    {
        $claim = Claims::where('id', $itemID)->first();

        return view('profile.purchases.show')->with(compact('claim'));
    }

}