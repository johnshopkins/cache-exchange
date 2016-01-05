<?php

namespace CacheExchange;

class Cache
{
	protected $datastore;
	protected $keymaker;

	public function __construct(\CacheExchange\Interfaces\Datastore $datastore, $keymaker = null)
	{
		$this->datastore = $datastore;
		
		if (is_callable($keymaker)) {
			$this->keymaker = $keymaker;
		}
	}

	/**
	 * Makes a cache key based on the keymaker
	 * or passed query string parameters
	 * 
	 * @param  string 	$data Data to use to make the cache key
	 * @return string
	 */
	protected function makeKey($data)
	{
		if ($this->keymaker) {
			
			return call_user_func($this->keymaker, $data);
			
		} else {
			
			// default. Make the data into an array and sort.
			
			if (!is_array($data)) {
				$data = array($data);
			}
			ksort($data);
			return http_build_query($data);
			
		}
		
	}

	/**
	 * Stores the passed value in the cache.
	 * 
	 * @param 	mixed 		$value  	The value to store
	 * @param 	array 		$params 	Array of values or key/value pairs
	 *                          		that describe the data being stored.
	 * @param  	integer 	$ttl 		Time to live in seconds
	 * @param   boolean $makeKey  Convert params into a key. Pass false
	 *                            if you already have the raw key.
	 * @return 	boolean		TRUE on suceess; FALSE on failure.
	 */
	public function store($value, $params, $ttl = null, $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($params) : $params;
		return $this->datastore->store($key, $value, $ttl);
	}

	/**
	 * Fetches a stored value from the cache.
	 * 
	 * @param 	array 	$params 	Array of values or key/value pairs
	 *                          	that describe the data being fetched.
	 * @param   boolean $makeKey  Convert params into a key. Pass false
	 *                            if you already have the raw key.
	 * @return mixed 	The stored value on success; FALSE on failure.
	 */
	public function fetch($params, $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($params) : $params;
		return $this->datastore->fetch($key);
	}

	/**
	 * Checks to see if the key already exists in the cache
	 * 
	 * @param 	array 	$params 	Array of values or key/value pairs
	 *                          	that describe the key being looked up.
	 * @param   boolean $makeKey  Convert params into a key. Pass false
	 *                            if you already have the raw key.
	 * @return boolean	TRUE if the key exists; FALSE if it does not exist.
	 */
	public function exists($params, $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($params) : $params;
		return $this->datastore->exists($key);
	}

	/**
	 * [delete description]
	 * @param 	array 	$params 	Array of values or key/value pairs
	 *                          	that describe the key to be deleted.
	 * @param   boolean $makeKey  Convert params into a key. Pass false
	 *                            if you already have the raw key.
	 * @return boolean	TRUE on suceess; FALSE on failure.
	 */
	public function delete($params, $makeKey = true)
	{
		$key = $makeKey ? $this->makeKey($params) : $params;
		return $this->datastore->delete($key);
	}

	/**
	 * Completely lears the cache.
	 * 
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
