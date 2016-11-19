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

	private $monitor;
	private $queue;

	public function __construct(Queue $queue, Monitor $monitor, $workerId)
	{
		$this->queue = $queue;
		$this->monitor = $monitor;

		$this->log('Set up worker ' . $workerId);

		$this->workerId = $workerId;
		$this->workerHash = md5(rand(0,99999999999));
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

			$this->updateStatus('stopping');
		} catch (\Exception $e) {
			$this->updateStatus('aborting');

			$this->log("Exception Caught: ".$e->getMessage());
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

	private function checkStatus()
	{
		$this->updateStatus('checking status');

		if (time() > $this->endTime) {
            $this->log("Worker has expired");
            $this->stop();
        }
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
		} catch (\Exception $e) {
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

	private function updateStatus($status)
	{
		$this->status = $status;

		$this->takeSnapshot();
	}

	private function takeSnapshot()
	{
		$this->getMonitor()->takeSnapshot([
			"workerId" => $this->workerId,
			"workerHash" => $this->workerHash,
			"timeLimit" => $this->timeLimit,
			"endTime" => $this->endTime,
			"status" => $this->status
		]);
	}

	private function log($message)
	{
		$log = '[' . gmdate('H:i:s') . '] ' . $message;

		$this->getMonitor()->log($log);
	}

	private function getMonitor()
	{
		return $this->monitor;
	}
}