<?php

namespace CacheExchange\Interfaces;

interface DatastoreInterface
{
  /**
   * Set a value in the cache
   * @param string $key  Cache key
   * @param mixed $value Value to assign to the cache key
   * @param int $ttl     Time, in seconds, to cache the value
   * @return bool
   */
	public function set(string $key, mixed $value, int $ttl = 0): bool;

  /**
   * Get a value from the cache
   * @param string $key  Cache key
   * @return bool
   */
	public function get(string $key): mixed;

  /**
   * See if a value exists in the cache
   * @param string $key  Cache key
   * @return bool
   */
	public function exists(string $key): bool;

  /**
   * Delete a value from the cache
   * @param string $key  Cache key
   * @return bool
   */
	public function delete(string $key): bool;

  /**
   * Clear the cache
   * @return bool
   */
	public function clear(): bool;

  /**
   * Get all keys present in the cache
   * @return bool
   */
  public function getKeys(): array|false;
}
