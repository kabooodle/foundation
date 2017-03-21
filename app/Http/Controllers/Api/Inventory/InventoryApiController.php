<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use DB;
use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\Inventory;
use Illuminate\Validation\ValidationException;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;
use Kabooodle\Bus\Commands\Inventory\DeleteInventoryFromSaleCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFlashsaleListingCommand;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;
use Kabooodle\Foundation\Exceptions\Listings\ListingClaimableDateIsBeforeListingDateException;

/**
 * Class InventoryApiController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryApiController extends AbstractApiController
{
    use PaginatesTrait;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Begin the user inventory query.
            $groupings = [];
            $inventory = Inventory::noEagerLoads()->active()->with(['claims', 'style', 'styleSize', 'files'])
                ->where('user_id', '=', $this->getUser()->id)->get();
            $grouped = $inventory->groupBy('inventory_type_styles_id');
            foreach($grouped as $styleId => $items) {
                $groupings[$styleId] = [
                    'name' => null,
                    'total' => $items->sum('initial_qty'),
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
                        $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] = isset($groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty']) ? $groupings[$styleId]['subgroupings'][$item->styleSize->id]['total_qty'] + $item->initial_qty : $item->initial_qty;
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
                            'cover_photo' => $item->cover_photo->location,
                            'hash_id' => $item->hash_id,
                        ];
                    }

                    // Sort based on the order key.
                    usort($groupings[$styleId]['subgroupings'], function ($item1, $item2) {
                        return $item1['order'] <=> $item2['order'];
                    });
                }
            }

            sort($groupings);

            return $this->setData($groupings)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData(['msg' => trans('alerts.error_generic_retry')])
                ->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailed(Request $request)
    {
        $q = DB::table('listables as l')
            ->leftJoin('inventory_type_styles as s', 's.id','=', 'l.inventory_type_styles_id')
            ->leftJoin('inventory_sizes as is', 'is.id', '=', 'l.inventory_sizes_id')
            ->leftJoin('claims as c', function($q){
                $q->on('c.listable_id', '=', 'l.id');
                $q->on('c.listable_type', '=', 'l.subclass_name');
            })
            ->leftJoin('v_pageviews as v', function($q){
                $q->on('v.viewable_id', '=', 'l.id');
                $q->on('v.viewable_type', '=', 'l.subclass_name');
            })
            ->join('files as f', 'f.id', '=', 'l.cover_photo_file_id')
            ->where('l.user_id', '=', $this->getUser()->id)
            ->where('l.subclass_name', '=', Inventory::class)
            ->whereNull('l.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('l.archived_at')
            ->selectRaw(DB::raw("
            l.id,
            l.subclass_name,
            l.name,
            l.name_alt,
            l.slug,
            CONCAT(l.name_alt,'_', l.id) as name_with_id,
            CONCAT(l.name_alt, '::', f.location) as name_with_cover_photo,
            f.location as cover_photo_location,
            CONCAT('$', IFNULL(SUM(c.accepted_price), 0)) AS accepted_price_sum,
            CONCAT('$', IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0)) AS gross,
            IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
            IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
            IFNULL(SUM(v.count), 0) AS pageviews_count,
            l.initial_qty as qty_on_hand
            "))
            ->groupBy(DB::raw('name_with_id asc WITH rollup'));

        if (Binput::has('filter')) {
            $filter = Binput::get('filter');
            $q->where(function($q) use ($filter) {
                $q->where('l.name', 'like', '%'.$filter.'%');
                $q->orWhere('l.name_alt', 'like', '%'.$filter.'%');
            });
        }

        $select = $q->get();

        if ($select) {
            // I've intentionally added WITH ROLLUP to the query to put the requirement of summing/totalling on MYSQL
            // However, we dont want to display this with the data. So remove it from the array and add it elsewhere.
            $rolledup = array_pop($select);
            $rolledupData = [
                'accepted_price_sum' => $rolledup->accepted_price_sum,
                'gross' => $rolledup->gross,
                'accepted_sales_count' => $rolledup->accepted_sales_count,
                'pending_sales_count' => $rolledup->pending_sales_count,
                'pageviews_count' => $rolledup->pageviews_count,
                'qty_on_hand' => $rolledup->qty_on_hand,
            ];
        }

        $data = collect($select);

        $data = $this->paginateData($request, $data);

        return \Response::json(['data' => $data, 'totals' => isset($rolledupData) ? $rolledupData : []]);
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
            $item = $this->getUser()->inventory()->findOrFail($id);
            $this->validate($request, Inventory::getUpdateRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' =>'You must add at least 1 image.']);

            $categories = implode(',',Binput::get('categories', []));

            $this->dispatchNow(new UpdateInventoryItemCommand(
                $this->getUser(),
                $item,
                (int) Binput::get('style_id'),
                (int) Binput::get('size_id'),
                (float) Binput::get('price_usd'),
                (float) Binput::get('wholesale_price_usd', 0),
                (int) Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('description'),
                $categories,
                Binput::get('uuid')
            ));

            return $this->setData(['msg' => "Item {$item->name} updated", 'item' => $item->toJson()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
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
     *
     * @return \Illuminate\Http\Response
     */
    public function associate(Request $request)
    {
        $listingType = Binput::get('listingtype');
        $flashsaleId = Binput::get('flashsale', null);
        $selectedItems = (array) Binput::get('items', []);

        // Facebook data
        $facebookAlbums = (array) Binput::get('fb_albums', []);
        $facebookGroup = Binput::get('fb_group', null);
        $facebookGroupId = $facebookGroup ? $facebookGroup['id'] : null;

        // Facebook sales options, are, optional :)
        $options = (array) Binput::get('options', []);

        // Date to list it and remove it
        $listAt = array_get($options, 'list_at', null);
        $removeAt = array_get($options,'remove_at', null);
        // Date range you can claim.
        $claimableAt = array_get($options, 'available_at', null);
        $claimableUntil = array_get($options, 'available_until', null);
        $itemMessage = array_get($options, 'item_message', false);

        try {
            // You must have either a flashsaleid or facebookalbum
            if (($listingType == 'flashsale' && ! $flashsaleId ) || ($listingType == 'facebook' && count($facebookAlbums) == 0)) {
                throw new MissingMandatoryParametersException('You must select a sale');
            }

            if (count($selectedItems) == 0) {
                throw new MissingMandatoryParametersException;
            }

            if ($claimableAt && strtotime($claimableAt) < strtotime($listAt)) {
                throw new ListingClaimableDateIsBeforeListingDateException('The earliest date an item can be claimed cannot come before the listing date.');
            }

            $listingOptions = new FacebookListingOptions($listAt, $removeAt, $claimableAt, $claimableUntil, $itemMessage);

            if ($listingType == 'flashsale' && $flashsaleId) {
                $command = new ScheduleFlashsaleListingCommand($this->getUser(), $flashsaleId, $selectedItems);
            } else {
                $command = new ScheduleFacebookListingCommand(
                    $this->getUser(),
                    $facebookAlbums,
                    $facebookGroupId,
                    $selectedItems,
                    $listingOptions
                );
            }

            $this->dispatchNow($command);

            return $this->setData(['msg' =>'Items scheduled successfully to sale.'])->respond();
        } catch (FacebookAuthenticationException $e) {
            $msg = 'Your facebook credentials are invalid. Please re-authorize '.env('APP_NAME').' for your facebook account, via our settings page.';
            return $this->setData(['msg' => $msg])->setStatusCode(500)->respond();
        }catch (MissingMandatoryParametersException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage() ? : 'You must select as least 1 item for listing.'])->respond();
        } catch (ListingConflictsWithExistingListingException $e) {
            return $this->setStatusCode(500)->setData(['msg' => 'The date and time block you selected conflicts with an existing listing. Please select a new block of time.'])->respond();
        } catch (Exception $e){
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $flashSaleItemId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAssociation(Request $request, $username, $flashSaleItemId)
    {
        $this->dispatchNow(new DeleteInventoryFromSaleCommand($this->getUser(), $flashSaleItemId));

        return $this->noContent();
    }
}