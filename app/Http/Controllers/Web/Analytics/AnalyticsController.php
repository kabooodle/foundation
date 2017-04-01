<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Analytics;

use Kabooodle\Services\Keen\KeenService;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class AnalyticsController
 */
class AnalyticsController extends Controller
{
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

    public function index()
    {
        // https://github.com/keen/keen-js/blob/master/docs/dataviz/c3.md
        // https://github.com/keen/keen-js/blob/master/docs/visualization.md
        // https://github.com/keen/keen-js
        // https://github.com/keen/dashboards/blob/gh-pages/examples/starter-kit/keen.dashboard.js
        return $this->view('analytics.index');
    }
}
