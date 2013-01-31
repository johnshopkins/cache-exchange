<?php

namespace CacheExchange\Adapters;

class APC implements \CacheExchange\Interfaces\Datastore
{
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
}