<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Plans;

/**
 * Interface PlanInterface
 * @package Kabooodle\Models\Plans
 */
interface PlanInterface
{
    public function getAmount();
    public function getInterval();
    public function getID();
    public function getAlias();
    public function getDescription();
    public function hasTrial();
    public function getTrialDays();
}
