<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Binput;
use Bugsnag;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Traits\FilterListingItemsTrait;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;
use Kabooodle\Bus\Commands\Flashsale\DeleteFlashsaleCommand;
use Kabooodle\Transformers\Flashsales\FlashsaleListingItemTransformer;
use Kabooodle\Transformers\Flashsales\FlashsalesTransformer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleTimeSlotDateException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidEndDateException;
use Kabooodle\Foundation\Exceptions\Flashsales\FlashsaleInvalidStartDateException;

/**
 * Class FlashsalesApiController
 */
class FlashsalesApiController extends AbstractApiController
{
    use DispatchesJobs, PaginatesTrait, FilterListingItemsTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $this->getUser();

        // We are using the query builder so we can paginate on the query vs
        // returning everything, then chunking the collection of data.
        $data = FlashSales::withoutExpired()
            ->orderByStartDate()
            ->with('coverimage', 'watchers');

        if ($searchName = Binput::get('name', false)) {
            $data = $data->where('name', 'LIKE', '%' . $searchName . '%');
        }

        $data = $data->paginate();

        // Filter through the items and hide private items where the user is not
        // a seller.  Reminder, sellers include admins, owner, sellers.
        $data->setCollection($data->filter(function (FlashSales $flashsale) use ($user) {
            return $flashsale->canUserViewPrivateSale($user);
        }));

        return $this->response->paginator($data, new FlashsalesTransformer);
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

            $startsEnds = new StartsAndEndsAt(Binput::get('starts_at'), Binput::get('ends_at'));

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
                    $timeslot = $this->normalizeDate(array_get($sellerGroup, 'time_slot', null));
                    $sellerGroup['time_slot'] = $timeslot;
                    if ($timeslot && $timeslot < $startsEnds->getStartsAt()) {
                        throw new FlashsaleTimeSlotDateException('Time slot (' . $sellerGroup['time_slot'] . ' for seller group must be within flashsale date range.');
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
                $sellerGroups,
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

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, FlashSales::getUpdateRules());

            $flashsale = FlashSales::findOrFail($id);

            if (! $flashsale->canSellerListToFlashsaleAnytime($this->getUser()->id)) {
                throw new AccessDeniedHttpException;
            }

            $sellerGroups = Binput::get('seller_groups', []);

            // Seller groups with time_slot's must be within the flashsales date range.
            if ($sellerGroups) {
                foreach ($sellerGroups as &$sellerGroup) {
                    $timeslot = $this->normalizeDate(array_get($sellerGroup, 'time_slot', null));
                    $sellerGroup['time_slot'] = $timeslot;
                    if ($timeslot && $timeslot < $flashsale->starts_at) {
                        throw new FlashsaleTimeSlotDateException('Time slot (' . $sellerGroup['time_slot'] . ' for seller group must be within flashsale date range.');
                    }
                }
            }

            $admins = Binput::get('admins', null);
            if ($admins) {
                $admins = collect($admins)->pluck('id')->toArray();
            }

            $this->dispatchNow(new UpdateFlashsaleCommand(
                $flashsale,
                $this->getUser(),
                Binput::get('name'),
                Binput::get('description'),
                Binput::get('privacy'),
                $admins,
                $sellerGroups,
                Binput::get('cover_photo')
            ));

            return $this->setData([
                'msg' => 'Flash sale successfully updated.',
                'redirect' => route('flashsales.edit', [$flashsale->getUUID()])
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(400)
                ->setData(['errors' => $e->validator->messages()])
                ->respond();
        } catch (FlashsaleTimeSlotDateException $e) {
            return $this->setStatusCode(400)
                ->setData(['msg' => $e->getMessage()])
                ->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            dd($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An error occurred please try again,'])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $flashsale = FlashSales::findOrFail($id);

            if (! $flashsale->canSellerListToFlashsaleAnytime($this->getUser()->id)) {
                throw new AccessDeniedHttpException;
            }

            $this->dispatchNow(new DeleteFlashsaleCommand($flashsale, $this->getUser()));

            return $this->setData([
                'msg' => 'Flash sale deleted successfully. One moment...',
                'redirect' => route('flashsales.index')
            ])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => 'An error occurred please try again,'])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $listing = FlashSales::with(['listingItems'])
                ->where('id', $id)
                ->first();

            if (! $listing) {
                throw new ModelNotFoundException;
            }

            $items = $listing->listingItems;

            $style_query = Binput::get('styles', []);
            $size_query = Binput::get('sizes', []);
            $sellers_query = Binput::get('sellers', []);

            $items = $this->filterListingItems($items, $style_query, $size_query, $sellers_query);

            return $this->response->paginator($this->paginateData($request, $items), new FlashsaleListingItemTransformer);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param null $date
     *
     * @return null
     */
    public function normalizeDate($date = null)
    {
        return ($date && $date <> '' && $date <> '0000-00-00 00:00:00' && $date <> 'Invalid date') ? Carbon::createFromFormat('m/d/Y h:ia', $date) : null;
    }
}
