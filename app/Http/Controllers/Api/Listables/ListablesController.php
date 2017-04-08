<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listables;

use Bugsnag;
use Exception;
use Response;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Claim\ClaimListedItemCommand;
use Kabooodle\Bus\Commands\Listables\ActivateListableCommand;
use Kabooodle\Bus\Commands\Listables\ArchiveListableCommand;
use Kabooodle\Bus\Commands\Listings\CreateListingItemCommand;
use Kabooodle\Bus\Commands\User\AddGuestCommand;
use Kabooodle\Foundation\Exceptions\Listables\ItemNotArchiveableBelongsToOutfitsException;
use Kabooodle\Models\Email;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;
use Binput;

/**
 * Class ListablesController
 * @package Kabooodle\Http\Controllers\Api\Listables
 */
class ListablesController extends AbstractApiController
{
    use PaginatesTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Begin the user inventory query.
            $groupings = [];
            $inventory = Inventory::noEagerLoads()->active()->with(['claims', 'style', 'styleSize', 'files'])
                ->where('user_id', '=', $this->getUser()->id)
                ->get();
            $grouped = $inventory->sortBy('style.name')->groupBy('inventory_type_styles_id');

            // Group them together in groups of 6
            $chunks = $grouped->chunk(1);

            // Get the current page
            $currentPage = $request->has('page') ? $request->get('page') : 1;

            // Create some basic pagination data
            $paginationData = [
                'current_page' => $currentPage,
                'next_page_url' =>  apiRoute('inventory.index', [webUser()->username]).'?page=' . ($currentPage + 1),
            ];

            // We the "next" chunk does not exist, set next page to null;
            if (!isset($chunks[$currentPage-1]) || ($chunks[$currentPage-1])->count() == 0) {
                $paginationData['next_page_url'] = null;
            }

            // Only iterate over the chunk for the page.
            if (isset($chunks[$currentPage-1])) {
                foreach ($chunks[$currentPage - 1] as $styleId => $items) {
                    $groupings[$styleId] = [
                        'name' => null,
                        'total' => $items->sum('initial_qty'),
                        'id' => $styleId,
                    ];
                    if ($items->count() > 0) {
                        foreach ($items as $item) {
                            if (!$groupings[$styleId]['name']) {
                                $groupings[$styleId]['name'] = $item->style->name;
                            }
                            $groupings[$styleId]['subgroupings'][$item->styleSize->id]['id'] = $item->styleSize->id;
                            $groupings[$styleId]['subgroupings'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                            $groupings[$styleId]['subgroupings'][$item->styleSize->id]['name'] = $item->styleSize->name;
                            $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] + $item->initial_qty : $item->initial_qty;
                            $groupings[$styleId]['subgroupings'][$item->styleSize->id]['listables'][] = [
                                'id' => $item->id,
                                'name_uuid' => $item->name_uuid,
//                                'uuid' => $item->uuid,
                                'name' => $item->name_with_variant,
                                'name_alt' => $item->name,
                                'initial_qty' => $item->initial_qty,
                                'available_qty' => $item->available_quantity,
                                'price_usd' => $item->price_usd,
                                'wholesale_price_usd' => $item->wholesale_price_usd,
                                'cover_photo' => $item->cover_photo->location,
//                                'hash_id' => $item->hash_id,
                            ];
                        }

                        // Sort based on the order key.
                        usort($groupings[$styleId]['subgroupings'], function ($item1, $item2) {
                            return $item1['order'] <=> $item2['order'];
                        });
                    }
                }
            }

            $outfits = InventoryGrouping::active()->whereUserId($this->getUser()->id)->with(['claims'])->orderBy('name')->get();

            if ($outfits->count() > 0) {
                $id = $outfits->count() + 1;
                $groupings[$id] = [
                    'name' => 'Outfits',
                    'total' => $outfits->sum('initial_qty'),
                    'id' => $id,
                ];
                foreach ($outfits as $item) {
                    $groupings[$id]['subgroupings'][$item->id]['id'] =$item->id;
                    $groupings[$id]['subgroupings'][$item->id]['order'] = 0;
                    $groupings[$id]['subgroupings'][$item->id]['name'] = $item->name;
                    $groupings[$id]['subgroupings'][$item->id]['total_qty'] = $item->available_quantity;
                    $groupings[$id]['subgroupings'][$item->id]['listables'][] = [
                        'id' => $item->id,
                        'name_uuid' => $item->name_uuid,
//                        'uuid' => $item->uuid,
                        'name' => $item->name_with_variant,
                        'name_alt' => $item->name,
                        'initial_qty' => $item->initial_qty,
                        'available_qty' => $item->available_quantity,
                        'price_usd' => $item->price_usd,
                        'wholesale_price_usd' => $item->wholesale_price_usd,
                        'cover_photo' => $item->cover_photo->location,
//                        'hash_id' => $item->hash_id,
                    ];
                }
            }

            sort($groupings);

            return $this->setData(['data' => $groupings, 'meta' => $paginationData])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, $id)
    {
        try {
            $listable = $this->getUser()->listables()->findOrFail($id);

            $this->dispatchNow(new ArchiveListableCommand(
                $listable,
                $this->getUser()
            ));

            return $this->setData([
                'msg' => "Item archived",
            ])->respond();
        } catch (ItemNotArchiveableBelongsToOutfitsException  $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => 'Item cannot be archived as it is currently associated to an outfit'])
                ->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request, $id)
    {
        try {
            $listable = $this->getUser()->listables()->findOrFail($id);

            $this->dispatchNow(new ActivateListableCommand(
                $listable,
                $this->getUser()
            ));

            return $this->setData([
                'msg' => "Item unarchived",
            ])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function claim(Request $request, $username, $id)
    {
        try {
            $listable = $this->getUser()->listables()->findOrFail($id);
            $claimer = User::find(Binput::get('claimer_id'));

            $listingItem = $this->dispatchNow(new CreateListingItemCommand(webUser(), $listable));
            if(!$claimer) {
                $this->validate($request, User::getGuestRules());

                // Does the email already exists in our system?
                $email = Email::whereAddress(trim($request->get('email')))->first();
                if ($email) {
                    $this->dispatchNow(new ClaimListedItemCommand($email->user, $listingItem, $listingItem->listedItem));
                } else {
                    $guest = $this->dispatch(new AddGuestCommand(
                        $request->get('first_name'),
                        $request->get('last_name'),
                        $request->get('email'),
                        $request->get('company'),
                        $request->get('street1'),
                        $request->get('street2'),
                        $request->get('city'),
                        $request->get('state'),
                        $request->get('zip'),
                        $request->get('phone')
                    ));

                    $this->dispatchNow(new ClaimListedItemCommand($guest, $listingItem, $listingItem->listedItem));
                }
            }

            if($listingItem && $claimer) {
                $this->dispatchNow(new ClaimListedItemCommand($claimer, $listingItem, $listable));
            }

            return $this->setData([
                'msg' => "Item claimed successfully!",
            ])->respond();
        } catch (Exception $e) {
//            return Response::json(['msg' => trans('alerts.error_generic_retry')], 500);
            return Response::json(['msg' => $e->getTraceAsString()], 500);
        }
    }
}
