<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listings;

use Binput;
use Bugsnag;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listings\CreateOrUpdateCustomListingCommand;

/**
 * Class ShoppablelinkApiController
 */
class ShoppablelinkApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, ['name' => 'required', 'id' => 'required|exists:listables,id']);

            $job = new CreateOrUpdateCustomListingCommand($this->getUser(), Binput::get('name'), [Binput::get('id')]);
            $this->dispatchNow($job);

            return $this->setData([
                'msg' => trans('alerts.listings.success_customlisting_save')
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setData([
                'msg' => $e->validator->getMessageBag()->first()
            ])->setStatusCode(401)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);

            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->respond();
        }
    }
}
