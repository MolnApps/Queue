<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Testing\SampleJob;
use \MolnApps\Queue\Testing\StopWorkerJob;
use \MolnApps\Queue\Testing\ThrowsExceptionJob;

use \MolnApps\Queue\Worker\BaseWorker;
use \MolnApps\Queue\Worker\Monitor;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SynchQueueManagerTest extends \PHPUnit_Framework_TestCase
{
	private $monitor;
	private $worker;

	protected function setUp()
	{
		// Create a queue
		$this->queue = QueueManager::make(['driver' => 'synch'])->getQueue();
	}

	/** @test */
	public function it_listens_for_a_job_with_beanstalk()
	{
		// And the queue is empty
		$this->queue->erase();

		// And I add some jobs to the queue
		$this->queue->addJob(SampleJob::class, ['message' => 'Foo']);
		$this->queue->addJob(SampleJob::class, ['message' => 'Bar']);
		$this->queue->addJob(ThrowsExceptionJob::class, ['message' => 'Baz']);
		$this->queue->addJob(SampleJob::class, ['message' => 'Foobar']);
		$this->queue->addJob(StopWorkerJob::class, []);

		// All the jobs are performed synchrounously
		$this->assertEquals(0, $this->queue->getJobsReady());
		$this->assertEquals(1, $this->queue->getJobsBuried());
		$this->assertEquals(4, $this->queue->getJobsDone());
	}
}