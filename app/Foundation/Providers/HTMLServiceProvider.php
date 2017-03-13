<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Collective\Html\HtmlBuilder;
use Collective\Html\FormBuilder;
use Collective\Html\HtmlServiceProvider as BaseHtmlServiceProvider;

/**
 * Class HTMLServiceProvider
 */
class HTMLServiceProvider extends BaseHtmlServiceProvider
{

    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function($app) {
            $url = $app['url'];
            if ($app->environment() == 'production') {
                $url = $app->make('Illuminate\Routing\UrlGenerator');
                $url->forceSchema('https');
            }

            return new HtmlBuilder($url,$app['view']);
        });
    }

    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function($app) {
            $url = $app['url'];
            if ($app->environment() == 'production') {
                $url = $app->make('Illuminate\Routing\UrlGenerator');
                $url->forceSchema('https');
            }

            $form = new FormBuilder($app['html'], $url, $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }
}
