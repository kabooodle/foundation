<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters;

/**
 * Class PresenterAbstract
 * @package Kabooodle\Presenters
 */
abstract class PresenterAbstract
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @var \Illuminate\Foundation\Application|\Illuminate\View\Factory|mixed
     */
    public $view;

    /**
     * @param mixed $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->view = app('view');
    }

    /**
     * Allow for property-style retrieval
     *
     * @param  $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->{$property}();
        }

        return $this->entity->{$property};
    }
}
