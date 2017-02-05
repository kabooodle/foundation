<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ShoppablelinkApiController
 */
class ShoppablelinkApiController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, ['name' => 'required']);

            return $this->noContent();
        } catch (ValidationException $e) {

        }
    }
}
