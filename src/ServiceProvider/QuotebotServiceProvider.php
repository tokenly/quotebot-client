<?php

namespace Tokenly\QuotebotClient\ServiceProvider;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/*
* QuotebotServiceProvider
*/
class QuotebotServiceProvider extends ServiceProvider
{

    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindConfig();

        $this->app->bind('Tokenly\QuotebotClient\Client', function($app) {
            $cache_store = app('Tokenly\QuotebotClient\LaravelCacheStore\LaravelCacheStore');
            $quotebot_client = new \Tokenly\QuotebotClient\Client(Config::get('quotebot.connection_url'), Config::get('quotebot.api_token'), $cache_store);
            return $quotebot_client;
        });
    }

    protected function bindConfig()
    {
        // simple config
        $config = [
            'quotebot.connection_url' => env('QUOTEBOT_CONNECTION_URL', 'http://quotebot.tokenly.co'),
            'quotebot.api_token'      => env('QUOTEBOT_API_TOKEN'     , null),
        ];

        // set the laravel config
        Config::set($config);

        return $config;
    }

}

