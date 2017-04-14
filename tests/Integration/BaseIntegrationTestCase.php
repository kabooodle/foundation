<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Tests\Integration;

use Kabooodle\Tests\BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class BaseIntegrationTestCase
 */
abstract class BaseIntegrationTestCase extends BaseTestCase
{
    use DatabaseTransactions;
}