<?php

namespace MolnApps\Queue\Beanstalk;

use \MolnApps\Queue\Driver\QueueDriver;
use \MolnApps\Queue\Driver\QueueDriverInspector;

use \Pheanstalk\Pheanstalk;
use \Pheanstalk\Exception\ServerException;

class Driver implements QueueDriver, QueueDriverInspector
{
	private $pheanstalk;
	private $tube;
	private $reserve;

	public function __construct(array $config)
	{
		$this->pheanstalk = new Pheanstalk($config['host']);
		$this->tube = $config['name'];
		$this->reserve = isset($config['reserve']) ? $config['reserve'] : 10;
	}

	public function addJob($serializedJob, $priority, $delay, $timeToRun)
	{
		$this->pheanstalk
			->useTube($this->tube)
			->put($serializedJob, $priority, $delay, $timeToRun);
	}

	public function getJob()
	{
		$job = $this->pheanstalk
			->watch($this->tube)
			->ignore('default')
			->reserve($this->reserve);

		if ($job) {
			return new Job($this->pheanstalk, $job);
		}

		return null;
	}

	public function getJobsReady()
	{
		return $this->getStats('current-jobs-ready');
	}

	public function getJobsBuried()
	{
		return $this->getStats('current-jobs-buried');
	}

	public function getUsing()
	{
		return $this->getStats('current-using');
	}

	private function getStats($key)
	{
		$stats = $this->pheanstalk->statsTube($this->tube);

		return $stats[$key];
	}

	public function erase()
	{
		$this->eraseWithMethod('peekReady');
		$this->eraseWithMethod('peekBuried');
	}

	private function eraseWithMethod($method)
	{
		try {
        	while($job = $this->pheanstalk->$method($this->tube)) {
				$this->pheanstalk->delete($job);
			}
		} catch(ServerException $e){
			// Do nothing
		}
	}
}