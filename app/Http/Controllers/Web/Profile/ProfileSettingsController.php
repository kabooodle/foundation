<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ProfileSettingsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSettingsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('profile.settings');
    }
}
