<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\InventoryGroupings;

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
use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;

/**
 * Class InventoryGroupingsController
 * @package Kabooodle\Http\Controllers\Api\Inventory
 */
class InventoryGroupingsController extends AbstractApiController
{
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
                implode(',', array_get($groupingData, 'categories', []))
            ));

            return $this->setData(['msg' => 'Outfit was created successfully', 'grouping' => $grouping->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e->getMessage()])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e->getMessage()])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e->getMessage()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => $e->validator->messages()])
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
                implode(',', array_get($groupingData, 'categories', []))
            ));

            return $this->setData(['msg' => 'Outfit '.$updated->name.' updated', 'grouping' => $updated->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => $e->validator->messages()])
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
