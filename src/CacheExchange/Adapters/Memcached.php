<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Memcached extension
 * http://www.php.net/manual/en/class.memcache.php
 */
class Memcached implements \CacheExchange\Interfaces\Datastore
{
  protected $cache;
  protected $compress;

  /**
   * [__construct description]
   * @param array $settings Array of settings
   *                        "connections" => array of array of settings (multiple servers) 
   *                        "compress"    => 
   */
  public function __construct($settings)
  {
    $this->cache = new \Memcached();

    foreach ($connections as $connection) {
      $this->cache->addServer($connection["host"], $connection["post"]);
    }

    $this->compress = $connections["compress"] ? MEMCACHE_COMPRESSED : null;
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
