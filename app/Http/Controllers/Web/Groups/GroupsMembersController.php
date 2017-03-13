<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Groups;

use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\Groups;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class GroupsMembersController
 * @package Kabooodle\Http\Controllers\Web\Groups
 */
class GroupsMembersController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param $groupIdAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function index($groupIdAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($groupIdAndName);
        $item = Groups::find($decryptedId);

        if ($item) {
            return $this->view('groups.members')->with(compact('item'));
        }

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
