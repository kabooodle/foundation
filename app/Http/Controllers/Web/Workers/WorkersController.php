<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Web\Workers;

use Kabooodle\Http\Controllers\Web\Controller;

class WorkersController extends Controller
{
    public function cron($job)
    {
        return response()->json([]);
    }
}