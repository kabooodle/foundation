<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Binput;
use Bugsnag;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleTimeSlotDateException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidEndDateException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidStartDateException;

/**
 * Class FlashsalesApiController
 */
class FlashsalesApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = FlashSales::withoutExpired()
            ->orderByStartDate()
            ->with('coverimage', 'listingItems', 'watchers');

        if ($searchName = Binput::get('name', false)) {
            $data = $data->where('name', 'LIKE', '%'. $searchName .'%');
        }

        $data = $data->paginate();

        return $this->setData($data)->respond();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, FlashSales::getRules());

            $startsEnds = new StartsAndEndsAt(
                strtotime(Binput::get('starts_at')),
                strtotime(Binput::get('ends_at'))
            );

            if ($startsEnds->getStartsAt() <= Carbon::now(current_timezone())) {
                throw new FlashsaleInvalidStartDateException('Start date must be after now.');
            }

            if ($startsEnds->getEndsAt() <= Carbon::now(current_timezone())) {
                throw new FlashsaleInvalidEndDateException('End date must be before now.');
            }

            if ($startsEnds->getEndsAt() < $startsEnds->getStartsAt()) {
                throw new FlashsaleInvalidEndDateException('End date must be after the start date.');
            }

            $sellerGroups = Binput::get('seller_groups', []);

            // Seller groups with time_slot's must be within the flashsales date range.
            if ($sellerGroups) {
                foreach ($sellerGroups as $sellerGroup) {
                    if (isset($sellerGroup['time_slot']) && ! is_null($sellerGroup)) {
                        $timeSlot = Carbon::createFromFormat('m/d/Y h:ia', $sellerGroup['time_slot']);
                        if ($timeSlot < $startsEnds->getStartsAt()) {
                            throw new FlashsaleTimeSlotDateException('Time slot ('.$sellerGroup['time_slot'].' for seller group must be within flashsale date range.');
                        }
                    }
                }
            }

            $admins = Binput::get('admins', null);
            if ($admins) {
                $admins = collect($admins)->pluck('id')->toArray();
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
            return $this->setStatusCode(400)
                ->setData(['errors' => ['starts_at' => [$e->getMessage()]]])
                ->respond();
        } catch (FlashsaleInvalidEndDateException $e) {
            return $this->setStatusCode(400)
                ->setData(['errors' => ['ends_at' => [$e->getMessage()]]])
                ->respond();
        } catch (FlashsaleTimeSlotDateException $e) {
            return $this->setStatusCode(400)
                ->setData(['msg' => $e->getMessage()])
                ->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An error occurred please try again,'])
                ->respond();
        }
    }
}
