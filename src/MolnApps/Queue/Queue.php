<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Driver\QueueDriver;

use \MolnApps\Queue\Job\JobSerializer;

class Queue
{
	private $driver;

	private $job;

	public function setDriver(QueueDriver $driver)
	{
		$this->driver = $driver;
	}

	public function getDriver()
	{
		if ( ! $this->driver) {
			throw new \Exception('Please provide a driver through the ::setDriver() method.');
		}

		return $this->driver;
	}

	public function addJob($jobName, array $data)
	{
		$serializedJob = JobSerializer::make($jobName, $data);

		$this->getDriver()->addJob($serializedJob);
	}

	public function getJob()
	{
		return $this->getDriver()->getJob();
	}

	public function __call($method, $args)
	{
		return call_user_func_array([$this->getDriver(), $method], $args);
	}
}