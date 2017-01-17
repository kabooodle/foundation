<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Binput;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidEndDateException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidStartDateException;

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

            if ($startsEnds->getStartsAt() <= Carbon::now()) {
                throw new FlashsaleInvalidStartDateException('Start date must be before now.');
            }

            if ($startsEnds->getEndsAt() <= Carbon::now()) {
                throw new FlashsaleInvalidEndDateException('End date must be before now.');
            }

            if ($startsEnds->getEndsAt() < $startsEnds->getStartsAt()) {
                throw new FlashsaleInvalidEndDateException('End date must be after the start date.');
            }

            $this->dispatchNow(new AddFlashsaleCommand(
                $this->getUser(),
                Binput::get('name'),
                Binput::get('description'),
                $startsEnds,
                Binput::get('privacy'),
                $admins,
                Binput::get('seller_groups', []),
                Binput::get('cover_photo', null)
            ));

            return $this->setData([
                'msg' => 'Flash sale successfully created. You can manage or update the settings at anytime.'
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(400)
                ->setData(['errors' => $e->validator->messages()])
                ->respond();
        } catch (FlashsaleInvalidStartDateException $e) {
            return $this->setStatusCode(500)
                ->setData(['errors' => ['starts_at' => [$e->getMessage()]]])
                ->respond();
        } catch (FlashsaleInvalidEndDateException $e) {
            return $this->setStatusCode(500)
                ->setData(['errors' => ['ends_at' => [$e->getMessage()]]])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An error occurred please try again,'])
                ->respond();
        }
    }
}
