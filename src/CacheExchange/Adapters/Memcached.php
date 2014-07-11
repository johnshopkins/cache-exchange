<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Memcached extension
 * http://www.php.net/manual/en/class.memcached.php
 */
class Memcached implements \CacheExchange\Interfaces\Datastore
{
  /**
   * Memcached object
   * @var object
   */
  protected $cache;

  /**
   * __construct
   * @param array $settings Array of settings
   *                        "connections" => array of array of settings (multiple servers) 
   */
  public function __construct($settings)
  {
    $this->cache = new \Memcached();
    $this->cache->addServers($settings["connections"]);
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

  public function getKeys()
  {
    return $this->cache->getAllKeys();
  }
}
