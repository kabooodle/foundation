<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Shoppables;

use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Models\Traits\ShoppableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class AbstractShoppable
 */
abstract class AbstractShoppable
{
    use EloquentDatesTrait, PresentableTrait, ShoppableTrait, SoftDeletes, UuidableTrait;
}