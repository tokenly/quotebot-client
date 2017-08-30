<?php

namespace Tokenly\QuotebotClient\LaravelCacheStore;

use Tokenly\QuotebotClient\Contracts\CacheStore;
use Illuminate\Contracts\Cache\Repository;

class LaravelCacheStore implements CacheStore {

    function __construct(Repository $repository) {
        $this->laravel_cache_store = $repository;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key) {
        return $this->laravel_cache_store->get($key);
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $minutes
     * @return void
     */
    public function put($key, $value, $minutes) {
        return $this->laravel_cache_store->put($key, $value, $minutes);
    }

}
