<?php

namespace Recoded\WpQuery;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Recoded\WpQuery\Database\Eloquent\Model;

class WpQueryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }

    public function register()
    {
        $this->app->resolving('db', function (DatabaseManager $database) {
            $database->extend('wordpress', function ($config, $name) {
                $config['name'] = $name;

                return new WordpressConnection($config, $name);
            });
        });
    }
}
