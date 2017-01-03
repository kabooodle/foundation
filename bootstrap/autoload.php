<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

define('KABOOODLE_START', microtime(true));

require __DIR__.'/../resources/helpers.php';
require __DIR__.'/../resources/model-helpers.php';
require __DIR__.'/../vendor/autoload.php';


$compiledPath = __DIR__.'/cache/compiled.php';

if (file_exists($compiledPath)) {
    require_once $compiledPath;
}
