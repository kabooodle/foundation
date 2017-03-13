<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Analytics;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class AnalyticsController
 */
class AnalyticsController extends Controller
{
    public function index()
    {
        return $this->view('analytics.index');
    }
}
