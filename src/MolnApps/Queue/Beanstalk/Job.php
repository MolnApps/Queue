<?php

namespace MolnApps\Queue\Beanstalk;

use \Pheanstalk\Pheanstalk;
use \Pheanstalk\Job as PheanstalkJob;

use \MolnApps\Queue\Driver\AbstractDriverJob;

class Job extends AbstractDriverJob
{
	private $pheanstalk;
	private $pheanstalkJob;
	private $job;

	public function __construct(Pheanstalk $pheanstalk, PheanstalkJob $job)
	{
		$this->pheanstalk = $pheanstalk;
		$this->pheanstalkJob = $job;
	}

	protected function getSerializedJob()
	{
		return $this->pheanstalkJob->getData();
	}

	public function delete()
	{
		$this->pheanstalk->delete($this->pheanstalkJob);
	}

	public function bury()
	{
		$this->pheanstalk->bury($this->pheanstalkJob);
	}
}