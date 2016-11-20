<?php

namespace MolnApps\Queue\Worker;

use \Predis\Client as Predis;

use Monolog\Logger;

class Monitor
{
	private static $instance;

	private $redis;
	
	public function __construct()
	{
		$this->redis = new Predis('tcp://127.0.0.1:6379');
	}

	public function setLogger(Logger $logger)
	{
		$this->log = $logger;
	}

	public function setHeartbeat(array $heartbeat)
	{
		$json = json_encode($heartbeat);
		$this->redis->hset('worker.status', $heartbeat['workerId'], $json);
	}

	public function getLastHeartbeat($workerId)
	{
		$json = $this->redis->hget('worker.status', $workerId);
		return json_decode($json);
	}

	public function log($message)
	{
		if ($this->log) {
			$this->log->notice($message);
		}
	}
}