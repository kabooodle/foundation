<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class FlashsalesApiController
 */
class FlashsalesApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, FlashSales::getRules());

            $admins = Binput::get('admins', null);
            if ($admins) {
                $admins = collect($admins)->pluck('id');
            }

            $startsEnds = new StartsAndEndsAt(
                strtotime(Binput::get('starts_at')),
                strtotime(Binput::get('ends_at'))
            );

            $this->dispatchNow(new AddFlashsaleCommand(
                $this->getUser(),
                Binput::get('name'),
                Binput::get('description'),
                $startsEnds,
                Binput::get('privacy'),
                $admins,
                Binput::get('seller_groups', [])
            ));

            return $this->noContent();
        } catch (ValidationException $e) {
            return $this->setStatusCode(400)
                ->setData(['msg' => 'Attention', 'errors' => $e->validator->messages()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An error occurred please try again,'])
                ->respond();
        }
    }
}
