<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
        $user = webUser();
        $invoices = $user->invoices();

        return $this->view('profile.subscription.invoices')->with(compact('user', 'invoices'));
    }

    /**
     * @param $invoiceId
     *
     * @return mixed
     */
    public function show($invoiceId)
    {
        return webUser()->findInvoiceOrFail($invoiceId)->view([]);
    }

    /**
     * @param $invoiceId
     *
     * @return mixed
     */
    public function download($invoiceId)
    {
        return webUser()->downloadInvoice($invoiceId, ['product' => 'kabooodle.com']);
    }
}
