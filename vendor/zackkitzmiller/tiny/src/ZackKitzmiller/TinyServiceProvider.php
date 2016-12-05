<?php namespace ZackKitzmiller;

use Illuminate\Support\ServiceProvider;

class TinyServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->package('zackkitzmiller/tiny', 'zackkitzmiller/tiny', __DIR__.'/../');

        $this->app['tiny.generate'] = $this->app->share(function($app)
        {
            return new TinyGenerateCommand($app['files']);
        });

        $this->commands('tiny.generate');
    }

    public function register()
    {
        $this->app['tiny'] = $this->app->share(function($app)
        {
            $key = $app['config']['zackkitzmiller/tiny::key'];

            return new Tiny($key);
        });
    }

    public function provides()
    {
        return array('tiny');
    }

}