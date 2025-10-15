<?php

namespace CacheExchange\Adapters;

/**
 * Uses PHP Memcached extension
 * http://www.php.net/manual/en/class.memcached.php
 */
class Memcached extends BaseAdapter implements \CacheExchange\Interfaces\DatastoreInterface
{
  /**
   * Memcached object
   * @var object
   */
  protected $cache;

  /**
   * Can memcached connect to the servers?
   * @var bool
   */
  public $ready = false;

  /**
   * @param $servers
   */
  public function __construct($servers = [])
  {
    $this->cache = new \Memcached();
    $this->cache->addServers($servers);

    if ($this->cache->getStats() !== false) {
      // can connect to servers
      $this->ready = true;
    }
  }

  public function set(string $key, mixed $value, int $ttl = 0): bool
  {
    if (!$this->ready) {
      return false;
    }

    $value = $this->maybeSerialize($value);

    return $this->cache->set($key, $value, $ttl);
  }

  public function get(string $key): mixed
  {
    if (!$this->ready) {
      return false;
    }

    $value = $this->cache->get($key);

    return $this->maybeUnserialize($value);
  }

  public function exists(string $key): bool
  {
    if (!$this->ready) {
      return false;
    }

    $data = $this->get($key);
    return !empty($data);
  }

  public function delete(string $key): bool
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->delete($key);
  }

  public function clear(): bool
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->flush();
  }

  public function getKeys(): array|false
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->getAllKeys();
  }
}
