<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listables;

use DB;
use Binput;
use Exception;
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

/**
 * Class ListablesController
 * @package Kabooodle\Http\Controllers\Api\Listables
 */
class ListablesController extends AbstractApiController
{
    use PaginatesTrait;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Begin the user inventory query.
        $groupings = [];
        $inventory = Inventory::noEagerLoads()->active()->with(['claims', 'style', 'styleSize', 'files'])
            ->where('user_id', '=', $this->getUser()->id)->get();
        $grouped = $inventory->groupBy('inventory_type_styles_id');
        foreach($grouped as $styleId => $items) {
            $groupings[$styleId] = [
                'name' => null,
                'total' => $items->count(),
                'id' => $styleId,
            ];
            if ($items->count() > 0) {
                foreach($items as $item) {
                    if(! $groupings[$styleId]['name']) {
                        $groupings[$styleId]['name'] = $item->style->name;
                    }
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['id'] = $item->styleSize->id;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['order'] = $item->styleSize->sort_order;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['name'] = $item->styleSize->name;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] : $item->initial_qty;
                    $groupings[$styleId]['subgroupings'][$item->styleSize->id]['listables'][] = [
                        'id' => $item->id,
                        'name_uuid' => $item->name_uuid,
                        'uuid' => $item->uuid,
                        'name' => $item->name_with_variant,
                        'name_alt' => $item->name,
                        'initial_qty' => $item->initial_qty,
                        'available_qty' => $item->available_quantity,
                        'price_usd' => $item->price_usd,
                        'wholesale_price_usd' => $item->wholesale_price_usd,
                        'cover_photo' => $item->cover_photo,
                        'hash_id' => $item->hash_id,
                    ];
                }

                // Sort based on the order key.
                usort($groupings[$styleId]['subgroupings'], function ($item1, $item2) {
                    return $item1['order'] <=> $item2['order'];
                });
            }
        }

        $outfits = InventoryGrouping::active()->whereUserId($this->getUser()->id)->with(['claims'])->orderBy('name')->get();

        if ($outfits->count() > 0) {
            $id = $outfits->count() + 1;
            $groupings[$id] = [
                'name' => 'Outfits',
                'total' => $outfits->count(),
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
                    'name' => $item->name,
                    'name_alt' => 'outfits',
                    'uuid' => $item->uuid,
                    'initial_qty' => $item->initial_qty,
                    'available_qty' => $item->available_quantity,
                    'price_usd' => $item->price_usd,
                    'wholesale_price_usd' => $item->wholesale_price_usd,
                    'cover_photo' => $item->cover_photo,
                    'hash_id' => $item->hash_id,
                ];
            }
        }

        sort($groupings);

        $data = [
//            'inventory' => $inventory,
            'groupings' => $groupings,
        ];

        return $this->setData($data)->respond();
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
                ->setData(['msg' => $e->getTraceAsString()])
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
                ->setData(['msg' => $e->getTraceAsString()])
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
        $listable = $this->getUser()->listables()->findOrFail($id);
        $claimer = User::find(Binput::get('claimer_id'));

        try {
            $listingItem = $this->dispatchNow(new CreateListingItemCommand(webUser(), $listable));
            if(!$claimer) {
                $this->validate($request, User::getGuestRules());

                // Does the email already exists in our system?
                $email = Email::whereAddress(trim($request->get('email')))->first();
                if ($email) {
                    $this->dispatchNow(new ClaimListedItemCommand($email->user, $listingItem, $listingItem->listedItem, true, $email));
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

                    $this->dispatchNow(new ClaimListedItemCommand($guest, $listingItem, $listingItem->listedItem, true, $guest->primaryEmail));
                }
            }

            if($listingItem && $claimer) {
                $this->dispatchNow(new ClaimListedItemCommand($claimer, $listingItem, $listable));
            }

            return $this->setData([
                'msg' => "Item claimed successfully!",
            ])->respond();
        } catch (Exception $e) {
            return Response::json(['message' => $e->getMessage()], 500);
        }
    }
}