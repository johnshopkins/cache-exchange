<?php

namespace CacheExchange\Adapters;

/*

Assumes this database structure:

CREATE TABLE `ce_cache` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cachekey` varchar(255) NOT NULL DEFAULT '',
  `cachevalue` longtext,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cachekey` (`cachekey`)
);

*/

class Database implements \CacheExchange\Interfaces\Datastore
{
	protected $connection;
	protected $pdo;

	public function __construct($connection)
	{
		$this->connection = $connection;
		$this->connect();
	}

	protected function connect()
	{
		extract($this->connection);
		$this->pdo = new \PDO("{$type}:dbname={$database};host={$host}", $username, $password);
	}

	public function update($key, $value, $seconds)
	{
		$values = array(
			"cachevalue" => $value,
			"expires" => time() + $seconds
		);

		$columns = array_keys($values);
		$columns = array_map(function($v) {
			return "{$v} = ?";
		}, $columns);

		$values = array_values($values);

		$sql = "UPDATE ce_cache SET " . implode(", ", $columns) . " WHERE cachekey = '{$key}'";

		$this->pdoPrepared = $this->pdo->prepare($sql);

		return $this->pdoPrepared->execute($values);
	}

	protected function create($key, $value, $seconds)
	{
		$values = array(
			"cachekey" => $key,
			"cachevalue" => $value,
			"expires" => time() + $seconds
		);

		$columns = implode(", ", array_keys($values));
		$values = array_values($values);

		$qMarks = implode(", ", array_fill(0, count($values), "?"));

		$sql = "INSERT INTO ce_cache ({$columns}) VALUES ({$qMarks})";

		$this->pdoPrepared = $this->pdo->prepare($sql);
		return $this->pdoPrepared->execute($values);
	}

	public function store($key, $value, $seconds)
	{
		if ($this->exists($key)) {
			$this->update($key, $value, $seconds);
		} else {
			$this->create($key, $value, $seconds);
		}		
	}

	public function fetch($key)
	{
		$now = time();
		$sql = "SELECT * FROM ce_cache WHERE cachekey = '{$key}' AND expires > {$now}";

		$this->pdoPrepared = $this->pdo->prepare($sql);
		$this->pdoPrepared->execute($values);

		$results = $this->pdoPrepared->fetchAll(\PDO::FETCH_ASSOC);
		$result = array_shift($results);

		return !empty($result) ? $result["cachevalue"] : null;
	}

	public function exists($key)
	{
		$sql = "SELECT * FROM ce_cache WHERE cachekey = '{$key}'";

		$this->pdoPrepared = $this->pdo->prepare($sql);
		$this->pdoPrepared->execute($values);

		$results = $this->pdoPrepared->fetchAll(\PDO::FETCH_ASSOC);

		return !empty($results);
	}

	public function delete($key)
	{
		$sql = "DELETE FROM ce_cache WHERE cachekey = '{$key}'";
		
		$this->pdoPrepared = $this->pdo->prepare($sql);
		return $this->pdoPrepared->execute();
	}

	public function clear()
	{
		$sql = "DELETE FROM ce_cache";
		
		$this->pdoPrepared = $this->pdo->prepare($sql);
		return $this->pdoPrepared->execute();
	}
}