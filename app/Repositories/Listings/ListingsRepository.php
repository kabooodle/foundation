<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Repositories\Listings;

use Kabooodle\Models\Listings;

/**
 * Class ListingsRepository
 */
class ListingsRepository implements ListingsRepositoryInterface
{
    /**
     * @var Listings
     */
    protected $model;

    /**
     * @param Listings $model
     */
    public function __construct(Listings $model)
    {
        $this->model = $model;
    }
}
