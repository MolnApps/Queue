<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Beanstalk\Driver as BeanstalkDriver;
use \MolnApps\Queue\Driver\SyncDriver;

class QueueManager
{
	private static $queueInstance;

	private $queue;

	public function __construct(array $config)
	{
		$this->queue = new Queue;
		$this->queue->setDriver($this->createDriver($config));
	}

	public static function make(array $config)
	{
		return new static($config);
	}

	private function createDriver($config)
	{
		switch ($config['driver']) {
			case 'beanstalk':
				return new BeanstalkDriver($config);
			default:
				return new SyncDriver;
		}
	}

	public function getQueue($name = null)
	{
		return $this->queue;
	}

	public function setAsGlobal()
	{
		static::$queueInstance = $this->queue;
	}

	public static function __callStatic($name, $args)
	{
		return call_user_func_array([static::$queueInstance, $name], $args);
	}
}