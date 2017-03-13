<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Groups;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Models\Groups;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class GroupsApiController
 * @Resource("Groups")
 * @Versions({"v1"})
 * @package Kabooodle\Http\Controllers\Api\Groups
 */
class GroupsApiController extends AbstractApiController
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, Groups::getRules());

            return $this->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->validator->messages()->first()])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData(['msg' => 'An error occurred, please try again.'])->respond();
        }
    }

    /**
     * @param $idAndName
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show($idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = user()->allMyGroups()->find($decryptedId);

        if ($item) {
            return $this->item($item);
        }

        return $this->response()->errorNotFound();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($idAndName)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
