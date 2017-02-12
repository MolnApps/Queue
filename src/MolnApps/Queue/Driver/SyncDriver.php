<?php

namespace MolnApps\Queue\Driver;

use \MolnApps\Queue\Job\JobSerializer;

class SyncDriver implements QueueDriver
{
	private $performedCount;
	private $buriedCount;

	public function addJob($serializedJob, $priority, $delay, $timeToRun)
	{
		$job = JobSerializer::take($serializedJob);

		try {
			$this->performJob($job);
		} catch (\Exception $e) {
			$this->buryJob($job);
		}
	}

	private function performJob($job)
	{
		$job->perform();
		++$this->performedCount;
	}

	private function buryJob($job)
	{
		++$this->buriedCount;
	}

	public function getJob()
	{
		// Do nothing
	}

	public function erase()
	{
		$this->performedCount = 0;
		$this->buriedCount = 0;
	}

	public function getJobsReady()
	{
		return 0;
	}

	public function getJobsBuried()
	{
		return $this->buriedCount;
	}

	public function getJobsDone()
	{
		return $this->performedCount;
	}
}