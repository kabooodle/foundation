<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\InventoryGroupings;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\InventoryGroupings\CreateInventoryGroupingCommand;
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
        $data = [
            'user' => $user,
            'groupings' => $user->inventoryGroupings,
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
        $data = [
            'user' => $user,
            'groupings' => $user->inventoryGroupings->find($groupingId),
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

            $this->validate($request, InventoryGrouping::getRules(), ['uuid.required' => 'The Unique ID field is required.', 'images.required' => 'You must add at least 1 image.']);

            $grouping = $this->dispatchNow(new CreateInventoryGroupingCommand(
                $this->getUser(),
                Binput::get('name'),
                (bool)Binput::get('locked'),
                (float)Binput::get('price_usd'),
                (int)Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('inventory_ids'),
                Binput::get('description'),
                implode(',', Binput::get('categories', []))
            ));

            return $this->setData(['msg' => 'Grouping {$grouping->name} created', 'grouping' => $grouping->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
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
    public function update(Request $request, $username, $groupingId)
    {
        try {
            $grouping = $this->checkIds($username, $groupingId);

            $this->validate($request, InventoryGrouping::getUpdateRules($groupingId), ['uuid.required' => 'The Unique ID field is required.', 'images.required' => 'You must add at least 1 image.']);

            $updated = $this->dispatchNow(new UpdateInventoryGroupingCommand(
                $this->getUser(),
                $grouping,
                Binput::get('name'),
                (bool)Binput::get('locked'),
                (float)Binput::get('price_usd'),
                (int)Binput::get('initial_qty'),
                Binput::get('images'),
                Binput::get('cover_photo'),
                Binput::get('inventory_ids'),
                Binput::get('description'),
                implode(',', Binput::get('categories', []))
            ));

            return $this->setData(['msg' => "Item {$updated->name} created", 'grouping' => $updated->toJson()])->respond();
        } catch (ForbiddenUserAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (ForbiddenModelAccessException $e) {
            return $this->setStatusCode(403)->setData(['msg' => $e])->respond();
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->setStatusCode(401)->setData(['msg' => $e])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)
                ->setData(['msg' => 'Some fields require input: '.$e->validator->messages()->first()])
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

            return $this->respond();
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
