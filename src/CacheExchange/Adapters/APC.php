<?php

namespace CacheExchange\Adapters;

class APC implements \CacheExchange\Interfaces\Datastore
{
	protected $cacheInfo = null;

	public function store($key, $value, $seconds)
	{
		return apc_store($key, $value, $seconds);
	}

	public function fetch($key)
	{
		return apc_fetch($key);
	}

	public function exists($key)
	{
		return apc_exists($key);
	}

	public function delete($key)
	{
		return apc_delete($key);
	}

	public function clear()
	{
		return apc_clear_cache();
	}

  public function getKeys()
  {
    if (is_null($this->cacheInfo)) {
      $this->cacheInfo = apc_cache_info("user");
    }

    $keys = array();

    foreach ($this->cacheInfo["cache_list"] as $item) {
      $keys[] = $item["info"];
    }
    
    return $keys;
  }
}
