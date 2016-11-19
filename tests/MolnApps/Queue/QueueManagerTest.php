<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Testing\SampleJob;
use \MolnApps\Queue\Testing\StopWorkerJob;
use \MolnApps\Queue\Testing\ThrowsExceptionJob;

use \MolnApps\Queue\Worker\BaseWorker;
use \MolnApps\Queue\Worker\Monitor;

class QueueTest extends \PHPUnit_Framework_TestCase
{
	private $monitor;
	private $worker;

	protected function setUp()
	{
		$this->queue = QueueManager::make([
			'driver' => 'beanstalk',
			'host' => '127.0.0.1',
			'name' => 'sample',
			'reserve' => '1',
		])->getQueue();

		$this->monitor = new Monitor;
		$this->worker = new BaseWorker($this->queue, $this->monitor, 1);
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

		$this->assertEquals(5, $this->queue->getJobsReady());
		$this->assertEquals(0, $this->queue->getJobsBuried());
		
		// When I have a worker listening for a job
		$this->worker->run();
		
		// The the worker performs all the jobs
		$this->assertEquals(0, $this->queue->getJobsReady());
		$this->assertEquals(1, $this->queue->getJobsBuried());

		// And takes a snapshot during the process
		$snapshot = $this->monitor->getSnapshot($this->worker->getWorkerId());
		$this->assertNotNull($snapshot);
		$this->assertContains($this->worker->getWorkerHash(), $snapshot);

	}
}