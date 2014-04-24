<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Memcache extension
 * http://www.php.net/manual/en/class.memcache.php
 */
class Memcache implements \CacheExchange\Interfaces\Datastore
{
    protected $cache;
    protected $compress;

    /**
     * [__construct description]
     * @param array $connection With keys "host", "port" and optional "compress"
     */
    public function __construct($settings = array())
    {
        $memcache = new \Memcache;
        $this->cache = $memcache->connect($settings['host'], $settings['port']);
        
        if (!$this->cache) {
            throw new \Exception('Cannot connect to ' . $connection["host"] . ':' . $connection["port"]);
        }

        $this->compress = $settings["compress"] ? MEMCACHE_COMPRESSED : null;
    }

    public function store($key, $value, $seconds)
    {
        if ($this->compress) {
            return $this->cache->set($key, $value, MEMCACHE_COMPRESSED, $seconds);
        } else {
            return $this->cache->set($key, $value, null, $seconds);
        }
    }

    public function fetch($key)
    {
        return $this->cache->get($key);
    }

    public function exists($key)
    {
        $data = $this->fetch($key);
        return !empty($data);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
    }

    public function clear()
    {
        return $this->cache->flush();
    }
}