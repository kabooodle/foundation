<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Web\Workers;

use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class WorkersController
 */
class WorkersController extends Controller
{
    public function cron()
    {
        

        return response()->json([]);
    }
}