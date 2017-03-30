<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

$app = new Kabooodle\Foundation\Application\KabooodleApplication(realpath(__DIR__.'/../'));

$app->singleton(Illuminate\Contracts\Http\Kernel::class, Kabooodle\Http\Kernel::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, Kabooodle\Console\Kernel::class);
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, Kabooodle\Foundation\Exceptions\Handler::class);

return $app;