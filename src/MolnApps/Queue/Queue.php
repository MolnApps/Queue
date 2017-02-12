<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Driver\QueueDriver;

use \MolnApps\Queue\Job\JobSerializer;

class Queue
{
	private $driver;

	private $jobParams = [];
	private $defaultJobParams = [
		'priority' => 1024,
		'delay' => 0,
		'timeToRun' => 60
	];
	
	private $job;

	public function __construct()
	{
		$this->setDefaultJobParams();
	}

	// ! Driver methods

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

	// ! Job methods

	public function addJob($jobName, array $data)
	{
		$serializedJob = JobSerializer::make($jobName, $data);

		$this->getDriver()->addJob($serializedJob, $this->getPriority(), $this->getDelay(), $this->getTimeToRun());

		$this->setDefaultJobParams();
	}

	public function getJob()
	{
		return $this->getDriver()->getJob();
	}

	// ! Job params methods

	public function withTimeToRun($timeToRun)
	{
		return $this->withJobParams(['timeToRun' => $timeToRun]);
	}

	public function withDelay($delay)
	{
		return $this->withJobParams(['delay' => $delay]);
	}

	public function withPriority($priority)
	{
		return $this->withJobParams(['priority' => $priority]);
	}

	public function withJobParams(array $jobParams)
	{
		$this->jobParams = array_merge($this->jobParams, $jobParams);

		return $this;
	}

	public function getTimeToRun()
	{
		return $this->getJobParam('timeToRun');
	}

	public function getDelay()
	{
		return $this->getJobParam('delay');
	}

	public function getPriority()
	{
		return $this->getJobParam('priority');
	}

	private function getJobParam($jobParam)
	{
		return isset($this->jobParams[$jobParam]) ? $this->jobParams[$jobParam] : null;
	}

	private function setDefaultJobParams()
	{
		$this->jobParams = $this->defaultJobParams;
	}

	// ! Magic methods

	public function __call($method, $args)
	{
		return call_user_func_array([$this->getDriver(), $method], $args);
	}
}