<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Traits\ResponseTrait;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class AbstractApiController
 * @package Kabooodle\Http\Controllers\Api
 */
abstract class AbstractApiController extends Controller
{
    use DispatchesJobs, Helpers, ResponseTrait, ValidatesRequests;
}
