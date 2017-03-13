<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Groups;

use Binput;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\Groups;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Kabooodle\Bus\Commands\Group\AddGroupCommand;

/**
 * Class GroupsController
 * @package Kabooodle\Http\Controllers\Web\Groups
 */
class GroupsController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function index(Request $request)
    {
        $data = webUser()->allMyGroups();

        $page = $request->get('page', 1);
        $perPage = config('pagination.per-page');

//        $chunk = array_slice($data->toArray(), $offset, $perPage, true);
        $data = new LengthAwarePaginator(
            $data->forPage($page, $perPage), // Only grab the items we need
            count($data), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->view('groups.index')->with(compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! webUser()->hasAtLeastMerchantSubscription()) {
            return redirect()->route('groups.index');
        }

        return $this->view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (! webUser()->hasAtLeastMerchantSubscription()) {
                return redirect()->route('groups.index');
            }

            $this->validate($request, Groups::getRules());

            $group = $this->dispatch(new AddGroupCommand(Binput::get('name'), Binput::get('members'), Binput::get('privacy'), user()));

            Messages::success("New group {$group->name}, was successfully created!");

            return $this->redirect(route('groups.index'));
        } catch (ValidationException $e) {
            Messages::error($e->validator->messages()->first());

            return $this->redirect(route('groups.create'))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show($idAndName)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = Groups::find($decryptedId);

        if ($item) {
            return $this->view('groups.show')->with(compact('item'));
        }

        return $this->redirect(route('groups.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($idAndName)
    {
        if (! webUser()->hasAtLeastMerchantSubscription()) {
            return redirect()->route('groups.index');
        }
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = webUser()->allMyGroups()->find($decryptedId);

        if ($item) {
            return $this->view('groups.edit')->with(compact('item'));
        }

        return $this->redirect(route('groups.index'));
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
