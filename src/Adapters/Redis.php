<?php

namespace CacheExchange\Adapters;

class Redis implements \CacheExchange\Interfaces\DatastoreInterface
{
  /**
   * @var \Predis\Client $redis
   */
  protected $cache;

  // /**
  //  * Can memcached connect to the servers?
  //  * @var bool
  //  */
  // public $ready = false;

  /**
   * @param $servers
   */
  public function __construct($servers = [])
  {
    $this->cache = new \Predis\Client();
  }

  public function set(string $key, mixed $value, int $ttl = 0): bool
  {
    if ($ttl > 0) {
      $status = $this->cache->setex($key, $ttl, $value);
    } else {
      $status = $this->cache->set($key, $value);
    }

    return in_array($status->getPayload(), ['OK', 'QUEUED']);
  }

  public function get(string $key): mixed
  {
    return $this->cache->get($key);
  }

  public function exists(string $key): bool
  {
    return $this->cache->exists($key);
  }

  public function delete(string $key): bool
  {
    return $this->cache->del($key) === 1;
  }

  public function clear(): bool
  {
    $status = $this->cache->flushall();
    return in_array($status->getPayload(), ['OK', 'QUEUED']);
  }

  public function getKeys(): array|false
  {
    return $this->cache->keys('*');
  }
}
