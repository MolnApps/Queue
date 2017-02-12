<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Driver\QueueDriver;

use \MolnApps\Queue\Job\JobSerializer;

class Queue
{
	private $driver;

	private $priority = 1024;
	private $delay = 0;
	private $timeToRun = 60;

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

	public function withTimeToRun($timeToRun)
	{
		$this->timeToRun = $timeToRun;

		return $this;
	}

	public function withDelay($delay)
	{
		$this->delay = $delay;

		return $this;
	}

	public function withPriority($priority)
	{
		$this->priority = $priority;

		return $this;
	}

	public function addJob($jobName, array $data)
	{
		$serializedJob = JobSerializer::make($jobName, $data);

		$this->getDriver()->addJob($serializedJob, $this->priority, $this->delay, $this->timeToRun);
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