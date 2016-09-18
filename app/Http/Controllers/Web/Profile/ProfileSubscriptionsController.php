<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ProfileSubscriptionsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSubscriptionsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = user();

        return $this->view('profile.subscription.index')->with(compact('user'));
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, $this->rules());

            $plan = Binput::get('p', false);

            $user = user();
        } catch (ValidationException $e) {
            dd($e);
        }
    }

    /**
     * @return array
     */
    private function rules()
    {
        return  [
            'p' => 'required'
        ];
    }
}