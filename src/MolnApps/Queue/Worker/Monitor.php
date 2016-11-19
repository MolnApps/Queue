<?php

namespace MolnApps\Queue\Worker;

use \Predis\Client as Predis;

class Monitor
{
	private static $instance;

	private $redis;
	
	public function __construct()
	{
		$this->redis = new Predis('tcp://127.0.0.1:6379');
	}

	public function takeSnapshot(array $snapshot)
	{
		$json = json_encode($snapshot);
		$this->redis->hset('worker.status', $snapshot['workerId'], $json);
	}

	public function log($message)
	{
		//echo $message . PHP_EOL;
	}

	public function getSnapshot($workerId)
	{
		return $this->redis->hget('worker.status', $workerId);
	}
}