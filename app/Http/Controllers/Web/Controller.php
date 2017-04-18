<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web;

use View;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

/**
 * Class Controller
 * @package Kabooodle\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, Helpers, ValidatesRequests;

    /**
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return View::make($view, $data, $mergeData);
    }

    /**
     * @param null  $to
     * @param int   $status
     * @param array $headers
     * @param null  $secure
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        return redirect($to, $status, $headers, $secure);
    }
}
