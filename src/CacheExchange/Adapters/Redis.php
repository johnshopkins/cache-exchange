<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Redis extension
 * http://www.php.net/manual/en/class.memcached.php
 */
class Redis implements \CacheExchange\Interfaces\Datastore
{
  /**
   * Redis object
   * @var object
   */
  protected $cache;

  /**
   * __construct
   * @param array $hostname 
   */
  public function __construct($hostname)
  {
    $this->cache = new Redis();
    $this->cache->connect($hostname);
  }

  public function store($key, $value, $seconds)
  {
    return $this->cache->set($key, $value, $seconds);
  }

  public function fetch($key)
  {
    return $this->cache->get($key);
  }

  public function exists($key)
  {
    return $this->cache->exists($key);
  }

  public function delete($key)
  {
    return $this->cache->del($key);
  }

  public function clear()
  {
    return $this->cache->flushAll();
  }

  public function getKeys()
  {
    return $this->cache->keys('*');
  }
}
