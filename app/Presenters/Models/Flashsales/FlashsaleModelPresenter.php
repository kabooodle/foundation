<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Flashsales;

use Carbon\Carbon;
use Kabooodle\Models\FlashSales;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class FlashsaleModelPresenter
 * @package Kabooodle\Presenters\Models\Flashsales
 */
class FlashsaleModelPresenter extends PresenterAbstract
{
    /**
     * @param null $timeslot
     *
     * @return string
     */
    public function timeslot($timeslot = null)
    {
        if ($timeslot) {
            return Carbon::parse($timeslot)->format('M d,y G:i:a');
        }

        return 'anytime ';
    }

    /**
     * @return string
     */
    public function getDateRange()
    {
        /** @var FlashSales $entity */
        $entity = $this->entity;

        return $entity->startsAtHuman().' - '.$entity->endsAtHuman();
    }
}
