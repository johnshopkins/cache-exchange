<?php

namespace CacheExchange\Interfaces;

interface Datastore
{
	public function store($key, $value, $seconds);
	public function fetch($key);
	public function exists($key);
	public function delete($key);
	public function clear();
}