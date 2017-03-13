<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api;

use Binput;
use Expception;
use Illuminate\Http\Request;
use Spatie\Newsletter\Newsletter;
use Illuminate\Validation\ValidationException;
use Spatie\Newsletter\NewsletterServiceProvider;

/**
 * Class GeneralController
 * @package Kabooodle\Http\Controllers\Api
 */
class GeneralController extends AbstractApiController
{
    /**
     * Ping endpoint allows API consumers to check the version.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return $this->item('Pong!');
    }

    /**
     * Endpoint to show the Cachet version.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function version()
    {
        $latest = getAppVersion();

        return $this->setMetaData([
            'latest'    => $latest,
        ])->item(getAppVersion());
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function joinClosedBeta(Request $request)
    {
        try {

            $this->validate($request, ['email' => 'required|email']);

            app()->register(NewsletterServiceProvider::class);
            $newsletter = app()->make(Newsletter::class);
            $newsletter->subscribe(Binput::get('email'));

            return $this->setData([
                'msg' => trans('alerts.closedbeta.join_success')
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)->setData([
                'msg' => $e->validator->messages()->first()
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->respond();
        }
    }
}
