<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use Carbon\Carbon;
use Kabooodle\Models\Files;
use Kabooodle\Models\FlashSales;

/**
 * Class UpdateFlashsaleCommandHandler
 * @package Kabooodle\Bus\Commands\Flashsale
 */
abstract class AbstractFlashsaleCommandHandler
{
    /**
     * @param array $array
     *
     * @return array
     */
    public function normalizeSellerGroups(array $array = [])
    {
        $groups = [];
        foreach ($array as $group) {
            $groupId = isset($group['id']) ? $group['id'] : false;
            $slot = $this->normalizeDate(array_get($group, 'time_slot', null));
            if ($groupId) {
                $groups[$group['id']]['time_slot'] = $slot;
            }
        }

        return $groups;
    }

    /**
     * @param null $date
     *
     * @return null|static
     */
    public function normalizeDate($date = null)
    {
        return ($date && $date <> '' && $date <> '0000-00-00 00:00:00' && $date <> 'Invalid date') ? Carbon::parse($date) : null;
    }

    /**
     * @param array      $data
     * @param FlashSales $flashsale
     *
     * @return static
     */
    public function buildCoverPhotoFromAWSData(array $data, FlashSales $flashsale)
    {
        $image = json_decode($data['json'], true);

        return Files::create([
            'location' => $image['location'],
            'key' => $image['key'],
            'bucket_name' => $image['bucket'],
            'fileable_type' => get_class($flashsale),
            'fileable_id' => $flashsale->id
        ]);
    }
}
