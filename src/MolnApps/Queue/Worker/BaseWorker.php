<?php

namespace MolnApps\Queue\Worker;

use \MolnApps\Queue\Queue;

use \MolnApps\Queue\Driver\AbstractDriverJob;

class BaseWorker implements Worker
{
	private $run = false;

	private $workerId;
	private $workerHash;
	private $status = 'init';

	private $timeLimit;
	private $startTime;
	private $endTime;

	private $jobsCount = 0;
	private $errorsCount = 0;

	private $monitor;
	private $queue;

	public function __construct(Queue $queue, Monitor $monitor, $workerId)
	{
		$this->queue = $queue;
		$this->monitor = $monitor;

		$this->workerId = $workerId;
		$this->workerHash = md5(rand(0,99999999999));

		$this->log('Set up worker ' . $workerId);

		$this->startTime = time();
        $this->timeLimit = 60 * 60 * 1; // Minimum of 1 hour
        $this->timeLimit += rand(0, 60 * 30); // Adding additional time between 0 to 30 minutes
        $this->endTime = $this->startTime + $this->timeLimit;

		$this->boot();
	}

	private function boot()
	{
		$this->log("Timeout Set to 0");
        set_time_limit(0);
	}

	public function getWorkerId()
	{
		return $this->workerId;
	}

	public function getWorkerHash()
	{
		return $this->workerHash;
	}

	public function run()
	{
		$this->log('Run worker ' . $this->workerHash);

		$this->run = true;
		
		try {
			$this->updateStatus('listening');

			while ($this->run) {
				$job = $this->queue->getJob();
				$this->processJob($job);
				$this->checkStatus();
			}

			$this->updateStatus('stopped');
		} catch (\Exception $e) {
			$this->updateStatus('aborting');

			$this->log('Exception caught: [' . $e->getMessage() . ']');
		}
	}

	private function processJob(AbstractDriverJob $job = null)
	{
		if ( ! $job) {
			return;
		}

		$this->updateStatus('processing job');

		$this->log('---');
		$this->log('Process job ' . $job->getIdentifier());

		if ($job->stopsWorker()) {
			$this->stop();
		} 
		
		return $this->handleJob($job);
	}

	public function stop()
	{
		$this->updateStatus('stopping worker');

		$this->log('Stop worker');

		$this->run = false;
	}

	private function handleJob(AbstractDriverJob $job)
	{
		$this->updateStatus('handling job');

		$this->log('Handle job');

		try {
			$this->performJob($job);
			$this->deleteJob($job);
			$this->increaseJobsCount();
		} catch (\Exception $e) {
			$this->increaseErrorsCount();
			$this->log('Exception caught: [' . $e->getMessage() . ']');
			$this->buryJob($job);
		}
	}

	private function performJob(AbstractDriverJob $job)
	{
		$this->updateStatus('performing job');
		$this->log('Perform job');

		$job->perform();
	}

	private function deleteJob(AbstractDriverJob $job)
	{
		$this->updateStatus('deleting job');
		$this->log('Delete job');

		$job->delete();
	}

	private function buryJob(AbstractDriverJob $job)
	{
		$this->updateStatus('burying job');
		$this->log('Bury job');

		$job->bury();
	}

	private function checkStatus()
	{
		$this->updateStatus('checking status');

		if (time() > $this->endTime) {
            $this->log("Worker has expired");
            $this->stop();
        }
	}

	private function updateStatus($status)
	{
		$this->status = $status;

		$this->heartbeat();
	}

	private function heartbeat()
	{
		$this->getMonitor()->setHeartbeat([
			'workerId' => $this->workerId,
			'workerHash' => $this->workerHash,
			'startTime' => $this->startTime,
			'timeLimit' => $this->timeLimit,
			'endTime' => $this->endTime,
			'status' => $this->status,
			'timestamp' => $this->freshTimestamp(),
			'jobsCount' => $this->jobsCount,
			'errorsCount' => $this->errorsCount,
		]);
	}

	private function log($message)
	{
		$log = '[' . $this->shortHash() . '] ' . $message;

		$this->getMonitor()->log($log);
	}

	private function getMonitor()
	{
		return $this->monitor;
	}

	private function freshTimestamp()
	{
		return gmdate("Y-m-d H:i:s");
	}

	private function shortHash()
	{
		return substr($this->workerHash, 0, 5);
	}

	private function increaseJobsCount()
	{
		++$this->jobsCount;
	}

	private function increaseErrorsCount()
	{
		++$this->errorsCount;
	}
}