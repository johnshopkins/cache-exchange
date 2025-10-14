<?php

namespace CacheExchange\Adapters;

class Redis extends BaseAdapter implements \CacheExchange\Interfaces\DatastoreInterface
{
  /**
   * @var \Predis\Client $redis
   */
  protected $cache;

  /**
   * Is Redis available?
   * @var bool
   */
  public $ready = false;

  /**
   * @var string|null
   */
  public $connectionError = null;

  /**
   * @param $connection
   */
  public function __construct($parameters = null, $options = null)
  {
    $this->cache = new \Predis\Client($parameters, $options);

    try {
      $this->cache->ping();
      $this->ready = true;
    } catch (\Throwable $e) {
      $this->connectionError = $e->getMessage();
    }
  }

  public function set(string $key, mixed $value, int $ttl = 0): bool
  {
    if (!$this->ready) {
      return false;
    }

    $value = $this->maybeSerialize($value);

    if ($ttl > 0) {
      $status = $this->cache->setex($key, $ttl, $value);
    } else {
      $status = $this->cache->set($key, $value);
    }

    return in_array($status->getPayload(), ['OK', 'QUEUED']);
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

    return $this->cache->exists($key);
  }

  public function delete(string $key): bool
  {
    if (!$this->ready) {
      return false;
    }

    return $this->cache->del($key) === 1;
  }

  public function clear(): bool
  {
    if (!$this->ready) {
      return false;
    }

    $status = $this->cache->flushall();
    return in_array($status->getPayload(), ['OK', 'QUEUED']);
  }

  public function getKeys(): array|false
  {
    if (!$this->ready) {
      return false;
    }
    
    return $this->cache->keys('*');
  }
}
