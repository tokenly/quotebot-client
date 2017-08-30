<?php

namespace Tokenly\QuotebotClient\Mock;

use Exception;
use Tokenly\QuotebotClient\Client;
use Tokenly\QuotebotClient\Contracts\CacheStore;

/**
*/
class MemoryCacheStore implements CacheStore
{

    protected $cache = [];
    protected $now = null;

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key) {
        if (!isset($this->cache[$key]) OR $this->cache[$key]['ttl'] <= $this->getNow()) {
            return null;
        }
        return $this->cache[$key]['value'];
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
        $this->cache[$key] = [
            'value' => $value,
            'ttl'   => $this->getNow() + ($minutes * 60),
        ];
    }

    public function clear() {
        $this->cache = [];
    }

    public function getNow() {
        return isset($this->now) ? $this->now : time();
    }

    public function setNow($now) {
        $this->now = $now;
    }

}