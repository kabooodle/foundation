<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models;

use Illuminate\Support\Collection;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class UserModelPresenter
 * @package Kabooodle\Presenters\Models
 */
class UserModelPresenter extends PresenterAbstract
{
    /**
     * @return array
     */
    public function getFacebookGroupsForList()
    {
        $groups = $this->entity->getFacebookGroups();
        $data = [];
        if ($groups) {
            foreach ($groups as $group) {
                $albums = $group['albums'];
                $data[$group['id']] = $group['name'].' ('.count($albums).' albums)';
            }
        }

        return $data;
    }

    /**
     * @param $groupId
     *
     * @return array
     */
    public function getFacebookAlbumsByFroupForList($groupId)
    {
        $groups = $this->entity->getFacebookGroups();
        $data = [];
        if ($groups) {
            $groups = new Collection($groups);
            $group = $groups->filter(function ($group) use ($groupId) {
                return $group['id'] == $groupId;
            })->first();

            foreach ($group['albums'] as $album) {
                $data[$album['id']] = $album['name'];
            }
        }

        return $data;
    }
}
