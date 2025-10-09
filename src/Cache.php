<?php

namespace CacheExchange;

class Cache
{
  /**
   * @var Interfaces\DatastoreInterface
   */
	protected $datastore;

  /**
   * @var callable
   */
	protected $keymaker;

  /**
   * @var string[]
   */
  protected $defaultKeymakers = ['array', 'string'];

	public function __construct(\CacheExchange\Interfaces\DatastoreInterface $datastore, $keymaker = 'string')
	{
		$this->datastore = $datastore;

    if (is_string($keymaker) && in_array($keymaker, $this->defaultKeymakers)) {
      $this->keymaker = [$this, "keymaker__$keymaker"];
    } elseif (is_callable($keymaker)) {
			$this->keymaker = $keymaker;
		} else {
      $this->keymaker = [$this, 'keymaker__string'];
    }
	}

  protected function keymaker__array(array|string $array): string
  {
    if (!is_array($array)) {
      $array = array($array);
    }

    ksort($array);
    return http_build_query($array);
  }

  protected function keymaker__string(string $string): string
  {
    return $string;
  }

  /**
   * Make a cache key
   * @param $data   Data to base the cache key on
   * @return string
   */
	protected function makeKey($data): string
	{
    return call_user_func($this->keymaker, $data);
	}

  /**
   * Set a value in the cache.
   * @param array|string $keyData Data to base the cache key on
   * @param mixed $value          The value to set
   * @param int|null $ttl         Time to live in seconds
   * @param bool $makeKey         Convert params into a key. Pass false if you already have the raw key.
   * @return bool
   */
	public function set(array|string $keyData, mixed $value, int $ttl = 0, bool $makeKey = true): bool
	{
		$key = $makeKey ? $this->makeKey($keyData) : $keyData;
		return $this->datastore->set($key, $value, $ttl);
	}

  /**
   * Get a value from the cache.
   * @param array|string $keyData Data to base the cache key on
   * @param bool $makeKey         Convert params into a key. Pass false if you already have the raw key.
   * @return mixed
   */
	public function get(array|string $keyData, bool $makeKey = true): mixed
	{
		$key = $makeKey ? $this->makeKey($keyData) : $keyData;
		return $this->datastore->get($key);
	}

  /**
   * Checks to see if the cache key exists in the cache.
   * @param array|string $keyData Data to base the cache key on
   * @param bool $makeKey         Convert params into a key. Pass false if you already have the raw key.
   * @return bool
   */
	public function exists(array|string $keyData, bool $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($keyData) : $keyData;
		return $this->datastore->exists($key);
	}

	/**
	 * [delete description]
	 * @param 	array|string 	$keyData 	Array of values or key/value pairs
	 *                          	that describe the key to be deleted.
	 * @param   boolean $makeKey  Convert params into a key. Pass false
	 *                            if you already have the raw key.
	 * @return boolean	TRUE on suceess; FALSE on failure.
	 */
	public function delete(array|string $keyData, bool $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($keyData) : $keyData;
		return $this->datastore->delete($key);
	}

	/**
	 * Clears the cache.
	 * @return boolean	TRUE on suceess; FALSE on failure.
	 */
	public function clear()
	{
		return $this->datastore->clear();
	}

	/**
	 * Get all keys stored in the cache
	 * @return array
	 */
	public function getKeys()
	{
		return $this->datastore->getKeys();
	}
}
