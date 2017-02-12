<?php

namespace MolnApps\Queue\Driver;

use \MolnApps\Queue\Job\JobSerializer;

use \MolnApps\Queue\Job\JobStopsWorker;
use \MolnApps\Queue\Job\Touchable;

abstract class AbstractDriverJob implements Touchable
{
	private $job;

	public function stopsWorker()
	{
		return 
			$this->getJob() instanceof JobStopsWorker && 
			$this->getJob()->shouldStopWorker();
	}

	public function getIdentifier()
	{
		return $this->getJob()->getIdentifier();
	}

	public function perform()
	{
		$this->getJob()->perform();
	}

	abstract public function delete();
	abstract public function bury();

	private function getJob()
	{
		if ( ! $this->job) {
			$this->job = JobSerializer::take($this->getSerializedJob());
			$this->job->setTouchable($this);
		}

		return $this->job;
	}

	abstract protected function getSerializedJob();
}