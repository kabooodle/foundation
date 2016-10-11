<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ProfileSubscriptionsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSubscriptionInvoicesController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = user();

        return $this->view('profile.subscription.invoices')->with(compact('user'));
    }

    /**
     * @param $invoiceId
     *
     * @return mixed
     */
    public function show($invoiceId)
    {
        return user()->findInvoiceOrFail($invoiceId)->view([]);
    }

    /**
     * @param $invoiceId
     *
     * @return mixed
     */
    public function download($invoiceId)
    {
        return user()->downloadInvoice($invoiceId, []);
    }
}