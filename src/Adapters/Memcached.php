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

  protected $ready = false;

  /**
   * __construct
   * @param array $settings Array of settings
   *                        "connections" => array of array of settings (multiple servers)
   */
  public function __construct($settings)
  {
    $this->cache = new \Memcached();
    $this->addServers($settings["connections"]);

    if ($this->cache->getStats() === false) {
      // cannot connect to servers
      return false;
    } else {
      // can connect to servers
      $this->ready = true;
    }
  }

  protected function addServers($connections)
  {
    // make sure servers aren't being duplcated
    $existingServers = $this->cache->getServerList();
    if (!empty($existingServers)) return;

    $this->cache->addServers($connections);
  }

  public function store($key, $value, $seconds)
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->set($key, $value, $seconds);
  }

  public function fetch($key)
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->get($key);
  }

  public function exists($key)
  {
    if (!$this->ready) {
      return false;
    }

    $data = $this->fetch($key);
    return !empty($data);
  }

  public function delete($key)
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->delete($key);
  }

  public function clear()
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->flush();
  }

  public function getKeys()
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->getAllKeys();
  }
}
