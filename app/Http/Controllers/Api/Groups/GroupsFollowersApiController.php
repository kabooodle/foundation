<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Groups;

use Kabooodle\Models\Groups;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class GroupsFollowersApiController
 * @package Kabooodle\Http\Controllers\Api\Groups
 */
class GroupsFollowersApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Dingo\Api\Http\Response\Factory|void
     */
    public function index(Request $request, $id)
    {
        if ($group = Groups::find($id)) {
            $this->setMetaData(['count' => $group->followers->count()]);
            return $this->setData($group->followers)->respond();
        }


        return $this->response()->errorUnauthorized();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Dingo\Api\Http\Response\Factory|void
     */
    public function store(Request $request, $id)
    {
        $me = $this->getUser();
        if ($group = Groups::find($id)) {
            if (! $group->is_followed) {
                $group->followers()->save($me);
                $group->load('followers');

                $this->setMetaData(['count' => $group->followers->count()]);
                return $this->setData($group->followers)->respond();
            }
        }

        return $this->response()->errorUnauthorized();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Dingo\Api\Http\Response\Factory|void
     */
    public function destroy(Request $request, $id)
    {
        $me = $this->getUser();
        if ($group = Groups::find($id)) {
            if ($group->is_followed) {
                $group->followers()->detach($me);
                $group->load('followers');
                $this->setMetaData(['count' => $group->followers->count()]);

                return $this->setData($group->followers)->respond();
            }
        }

        return $this->response()->errorUnauthorized();
    }
}
