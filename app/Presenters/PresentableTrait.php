<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters;

/**
 * Class PresentableTrait
 * @package Kabooodle\Presenters
 */
trait PresentableTrait
{
    /**
     * Prepare a new or cached presenter instance
     *
     * @return mixed
     * @throws PresenterException
     */
    public function present()
    {
        if (!$this->presenter or !class_exists($this->presenter)) {
            throw new PresenterException('Please set the $presenter property to your presenter path.');
        }

        return new $this->presenter($this);
    }
}
