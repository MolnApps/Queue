<?php

namespace MolnApps\Queue\Worker;

use \MolnApps\Queue\Queue;
use \MolnApps\Queue\Driver\AbstractDriverJob;
use \MolnApps\Queue\Testing\SampleJob;

class BaseWorkerTest extends \PHPUnit_Framework_TestCase
{
	private $queueStub;

	/** @before */
	protected function setUpWorker()
	{
		$this->queueStub = $this->createMock(Queue::class);
		$monitorStub = $this->createMock(Monitor::class);

		$this->worker = new BaseWorker($this->queueStub, $monitorStub, $workerId = 1);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->worker);
	}

	/** @test */
	public function it_returns_worker_id()
	{
		$this->assertEquals(1, $this->worker->getWorkerId());
	}

	/** @test */
	public function it_returns_worker_hash()
	{
		$this->assertNotNull($this->worker->getWorkerHash());
		$this->assertEquals(32, strlen($this->worker->getWorkerHash()));
		$this->assertTrue(ctype_alnum($this->worker->getWorkerHash()));
	}

	/** @test */
	public function it_can_be_run_and_stopped_with_a_job()
	{
		$quitJob = $this->createStopWorkerJob();

		$this->queueStub->expects($this->any())->method('getJob')->willReturn($quitJob);

		$this->worker->run();
	}

	/** @test */
	public function it_can_be_run_and_perform_jobs()
	{
		$jobStub = $this->createPerformableJob($this->returnValue(null));
		$jobStub->expects($this->once())->method('delete')->willReturn(null);

		$quitJob = $this->createStopWorkerJob();

		$this->queueReturnsJobs($this->onConsecutiveCalls($jobStub, $quitJob));

		$this->worker->run();
	}

	/** @test */
	public function it_can_be_run_and_bury_jobs()
	{
		$jobStub = $this->createPerformableJob($this->throwException(new \Exception));
		$jobStub->expects($this->once())->method('bury')->willReturn(null);

		$quitJob = $this->createStopWorkerJob();

		$this->queueReturnsJobs($this->onConsecutiveCalls($jobStub, $quitJob));

		$this->worker->run();
	}

	// ! Utility methods

	private function createStopWorkerJob()
	{
		return $this->createJob(true);
	}

	private function createPerformableJob($will)
	{
		$jobStub = $this->createJob(false);
		
		$jobStub->expects($this->once())->method('perform')->will($will);
		
		return $jobStub;
	}

	private function createJob($stopsWorker)
	{
		$jobStub = $this->createMock(AbstractDriverJob::class);
		
		$jobStub->expects($this->any())->method('stopsWorker')->willReturn($stopsWorker);

		return $jobStub;
	}

	private function queueReturnsJobs($will)
	{
		$this->queueStub->expects($this->any())->method('getJob')->will($will);
	}
}