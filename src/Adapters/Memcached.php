<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Memcached extension
 * http://www.php.net/manual/en/class.memcached.php
 */
class Memcached implements \CacheExchange\Interfaces\DatastoreInterface
{
  /**
   * Memcached object
   * @var object
   */
  protected $cache;

  /**
   * @param $servers
   */
  public function __construct($servers = [])
  {
    $this->cache = new \Memcached();
    $this->cache->addServers($servers);
  }

  public function set(string $key, mixed $value, int $ttl = 0): bool
  {
    return $this->cache->set($key, $value, $ttl);
  }

  public function get(string $key): mixed
  {
    return $this->cache->get($key);
  }

  public function exists(string $key): bool
  {
    $data = $this->get($key);
    return !empty($data);
  }

  public function delete(string $key): bool
  {
    return $this->cache->delete($key);
  }

  public function clear(): bool
  {
    return $this->cache->flush();
  }

  public function getKeys(): array|false
  {
    return $this->cache->getAllKeys();
  }
}
