<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use LynX39\LaraPdfMerger\PdfManage;
use Illuminate\Support\ServiceProvider;
use Kabooodle\Services\PdfMerger\PdfMerger;
use Kabooodle\Services\PdfMerger\PdfMergerInterface;

/**
 * Class MailServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class PdfMergerProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    public $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PdfMergerInterface::class, function ($app) {
            return new PdfMerger(new PdfManage);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [PdfMergerInterface::class];
    }
}
