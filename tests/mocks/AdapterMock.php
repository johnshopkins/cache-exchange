<?php

namespace CacheExchange\mocks;

use CacheExchange\Interfaces\DatastoreInterface;

class AdapterMock implements DatastoreInterface
{
  protected $cache = [];

  public function set(string $key, mixed $value, int $ttl = 0): bool
  {
    $this->cache[$key] =  $value;
    return true;
  }

  public function get(string $key): mixed
  {
    return $this->cache[$key] ?? false;
  }

  public function exists(string $key): bool
  {
    return array_key_exists($key, $this->cache);
  }

  public function delete(string $key): bool
  {
    unset($this->cache[$key]);
    return true;
  }

  public function clear(): bool
  {
    $this->cache = [];
    return true;
  }

  public function getKeys(): array|false
  {
    return array_keys($this->cache);
  }
}
