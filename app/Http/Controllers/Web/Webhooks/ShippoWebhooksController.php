<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ShippoWebhooksController
 */
class ShippoWebhooksController extends Controller
{
    /**
     * @param Request $request
     */
    public function handleWebhook(Request $request)
    {
        // Yah, uber topsecret webhook token o.O
        if (!$request->has('x') || $request->get('x') <> 'KuMQnR5hhzlM2Wk7q9aS') {
            return;
        }

        $payload = json_decode($request->getContent(), true);
    }
}
