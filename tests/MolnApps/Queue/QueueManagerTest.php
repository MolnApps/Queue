<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Testing\SampleJob;
use \MolnApps\Queue\Testing\StopWorkerJob;
use \MolnApps\Queue\Testing\ThrowsExceptionJob;

use \MolnApps\Queue\Worker\BaseWorker;
use \MolnApps\Queue\Worker\Monitor;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class QueueTest extends \PHPUnit_Framework_TestCase
{
	private $monitor;
	private $worker;

	protected function setUp()
	{
		// Create a queue
		$this->queue = QueueManager::make([
			'driver' => 'beanstalk',
			'host' => '127.0.0.1',
			'name' => 'sample',
			'reserve' => '1',
		])->getQueue();

		// Create a monitor with a logger
		$logger = new Logger('sample');
		$logger->pushHandler(new StreamHandler('/var/www/log/test.log', Logger::DEBUG));

		$this->monitor = new Monitor('sample');
		$this->monitor->setLogger($logger);

		// Create a worker
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
		$snapshot = $this->monitor->getLastHeartbeat($this->worker->getWorkerId());
		$this->assertNotNull($snapshot);
		$this->assertEquals($this->worker->getWorkerId(), $snapshot->workerId);
		$this->assertEquals($this->worker->getWorkerHash(), $snapshot->workerHash);
		$this->assertEquals(gmdate('Y-m-d H:i:s'), $snapshot->timestamp);
		$this->assertEquals(4, $snapshot->jobsCount);
		$this->assertEquals(1, $snapshot->errorsCount);
	}
}