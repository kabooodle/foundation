<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\InventoryGroupings;

use DB;
use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\InventoryGroupings\CreateInventoryGroupingsCommand;
use Kabooodle\Bus\Commands\InventoryGroupings\DestroyInventoryGroupingCommand;
use Kabooodle\Bus\Commands\InventoryGroupings\UpdateInventoryGroupingCommand;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Foundation\Exceptions\ForbiddenModelAccessException;
use Kabooodle\Foundation\Exceptions\ForbiddenUserAccessException;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;

/**
 * Class InventoryGroupingsController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryGroupingsController extends AbstractApiController
{
    use PaginatesTrait;
    
    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $username)
    {
        $user = User::whereUsername($username)->firstOrFail();
        $groupings = InventoryGrouping::with('inventoryItems')->whereUserId($user->id)->orderBy('name')->get();

        $data = [
            'user' => $user,
            'groupings' => $groupings,
        ];

        return $this->setData($data)->respond();
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
            ->where('l.subclass_name', '=', InventoryGrouping::class)
            ->whereNull('l.deleted_at')
            ->whereNull('c.deleted_at')
            ->selectRaw(DB::raw("
            l.id,
            l.subclass_name,
            l.name,
            l.name_alt,
            CONCAT(l.name_alt,'_', l.id) as name_with_id,
            CONCAT(l.name_alt, '::', f.location) as name_with_cover_photo,
            f.location as cover_photo_location,
            CONCAT('$', IFNULL(SUM(c.accepted_price), 0)) AS accepted_price_sum,
            CONCAT('$', IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0)) AS gross,
            IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
            IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
            IFNULL(SUM(v.count), 0) AS pageviews_count,
            IFNULL(SUM(l.initial_qty),0) as qty_on_hand
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
//
//        $sql = "
//            SELECT
//            l.id,
//            l.class,
//            l.name,
//            l.name_alt,
//            CONCAT(l.name_alt,'_', l.id) as name_with_id,
//            f.location as cover_photo_location,
//            CONCAT('$', IFNULL(SUM(c.accepted_price), 0)) AS accepted_price_sum,
//            CONCAT('$', IFNULL(SUM(CASE WHEN c.accepted = 1 THEN (CASE WHEN c.price IS NULL THEN c.accepted_price ELSE c.price END) ELSE 0 END),0)) AS gross,
//            IFNULL(SUM(c.accepted = 1), 0) AS accepted_sales_count,
//            IFNULL(SUM(c.accepted = null),0) AS pending_sales_count,
//            IFNULL(SUM(v.count), 0) AS pageviews_count,
//            IFNULL(SUM(l.initial_qty),0) as qty_on_hand
//            FROM listables as i
//            LEFT JOIN inventory_type_styles AS s ON s.id = l.inventory_type_styles_id
//            LEFT JOIN inventory_sizes as `is` ON is.id=l.inventory_sizes_id
//            LEFT JOIN claims as c ON c.listable_id = l.id AND c.listable_type = l.class
//            LEFT JOIN v_pageviews as v on v.viewable_id = l.id AND v.viewable_type = l.class
//            INNER JOIN files as f ON f.id = l.cover_photo_file_id
//            WHERE l.user_id = ?
//            AND l.deleted_at is null
//            and c.deleted_at is null
//            GROUP BY name_with_id asc
//            WITH rollup
//            ";
//
//        $select = DB::select($sql, [$this->getUser()->id]);

//
//        if ($request->has('style_id') && $request->get('style_id')) {
//            $data = $data->whereIn('inventory_type_styles_id', $request->get('style_id'));
//        }
//        if ($request->has('size_id') && $request->get('size_id')) {
//            $data = $data->whereIn('inventory_sizes_id', $request->get('size_id'));
//        }
//        if ($request->has('qty_0')) {
//            $data = $data->where('initial_qty', 0);
//        }
//        if ($request->has('flashsale_id')) {
//            $data = $data->whereHas('flashsales', function ($q) use ($request) {
//                $q->whereIn('flashsales.id', $request->get('flashsale_id'));
//            });
//        }
//        if ($request->has('has_sales')) {
//            $data = $data->has('sales', '>', 0);
//        }
//        if ($request->has('has_claims')) {
//            $data = $data->has('pendingClaims', '>', 0);
//        }

        // FIXME!!
        // Solution: Rewrite query as raw sql.
        // Problem: We are paginating the data in chunks of 50. However, we aren't calling paginate on the DB query,
        // but instead on the collection.  This is so that we can sort alphabetically correctly, which can
        // only be done on the results.  This pitfall of this is if we have a lot of data returned, for example 2000 items,
        // we would be sorting all 2000 items, then chunking it into 50.
        // If we used the native pagination on the query builder, it would only return chunks of 50 results at a time
        // lessening the overhead.  However, sorting the results alphabeitcally doesn't work.
//        $data = $data->get();


//        $data = $selec->sortBy(function($post) {
//            return sprintf('%-12s%s', $post->style->name, $post->styleSize->sort_order);
//        });

        if ($select) {
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
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $username, $groupingId)
    {
        $user = User::whereUsername($username)->firstOrFail();
        $grouping = InventoryGrouping::with('inventoryItems')->whereId($groupingId)->whereUserId($user->id)->firstOrFail();

        $data = [
            'user' => $user,
            'grouping' => $grouping,
        ];

        return $this->setData($data)->respond();
    }

    /**
     * @param Request $request
     * @param $username
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $username)
    {
        try {
            $this->checkIds($username);

            $this->validate($request, InventoryGrouping::getRules(), InventoryGrouping::getMessages());

            $groupingData = Binput::all();

            $grouping = $this->dispatchNow(new CreateInventoryGroupingsCommand(
                $this->getUser(),
                array_get($groupingData, 'name'),
                (bool)array_get($groupingData, 'locked'),
                (float)array_get($groupingData, 'price_usd'),
                (int)array_get($groupingData, 'initial_qty'),
                array_get($groupingData, 'image', []),
                array_get($groupingData, 'inventory', []),
                array_get($groupingData, 'description'),
                implode(',', array_get($groupingData, 'categories', [])),
                (bool)array_get($groupingData, 'auto_add'),
                (bool)array_get($groupingData, 'max_quantity')));

            return $this->setData(['msg' => 'Outfit was created successfully', 'grouping' => $grouping->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e->getMessage()])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e->getMessage()])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e->getMessage()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'There appears to be an issue with your input. Please correct the issue(s) an try again.', 'validationErrors' => $e->validator->messages()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => $e->getMessage()])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $username, $groupingId)
    {
        try {
            $grouping = $this->checkIds($username, $groupingId);

            $this->validate($request, InventoryGrouping::getUpdateRules($groupingId), InventoryGrouping::getMessages());

            $groupingData = Binput::all();

            $updated = $this->dispatchNow(new UpdateInventoryGroupingCommand(
                $this->getUser(),
                $grouping,
                array_get($groupingData, 'name'),
                (bool)array_get($groupingData, 'locked'),
                (float)array_get($groupingData, 'price_usd'),
                (int)array_get($groupingData, 'initial_qty'),
                array_get($groupingData, 'image', []),
                array_get($groupingData, 'inventory', []),
                array_get($groupingData, 'description'),
                implode(',', array_get($groupingData, 'categories', [])),
                (bool)array_get($groupingData, 'auto_add'),
                (bool)array_get($groupingData, 'max_quantity')));

            return $this->setData(['msg' => 'Outfit '.$updated->name.' updated', 'grouping' => $updated->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'There appears to be an issue with your input. Please correct the issue(s) an try again.', 'validationErrors' => $e->validator->messages()])
                ->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)
                ->setData(['msg' => $e])
                ->respond();
        }
    }

    /**
     * @param Request $request
     * @param $username
     * @param $groupingId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $username, $groupingId)
    {
        try {
            $grouping = $this->checkIds($username, $groupingId);

            $this->dispatchNow(new DestroyInventoryGroupingCommand($this->user(), $grouping));

            return $this->setData(['msg' => 'Outfit deleted'])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
        }
    }

    /**
     * @param $username
     * @param null $modelId
     *
     * @return mixed
     * @throws ForbiddenModelAccessException
     * @throws ForbiddenUserAccessException
     */
    private function checkIds($username, $modelId = null)
    {
        if ($this->user()->username !== $username) {
            throw new ForbiddenUserAccessException;
        }

        if ($modelId) {
            $model = InventoryGrouping::whereId($modelId)->firstOrFail();
            if ($this->user()->id !== $model->user_id) {
                throw new ForbiddenModelAccessException;
            }
            return $model;
        }
    }
}
