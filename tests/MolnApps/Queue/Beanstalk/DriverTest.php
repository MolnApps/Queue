<?php

namespace MolnApps\Queue\Beanstalk;

use \MolnApps\Queue\Driver\QueueDriver;
use \MolnApps\Queue\Driver\QueueDriverInspector;

use \MolnApps\Queue\Job\JobSerializer;
use \MolnApps\Queue\Testing\SampleJob;

class DriverTest extends \PHPUnit_Framework_TestCase
{
	private $driver;

	/** @before */
	public function setUpInstance()
	{
		$this->driver = new Driver(['host' => '127.0.0.1', 'name' => 'myTube', 'reserver' => 1]);
	}

	/** @after */
	public function tearDownInstance()
	{
		$this->driver->erase();
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$this->assertNotNull($this->driver);
		$this->assertInstanceOf(QueueDriver::class, $this->driver);
		$this->assertInstanceOf(QueueDriverInspector::class, $this->driver);
	}

	/** @test */
	public function it_adds_and_gets_a_job()
	{
		$this->assertEquals(0, $this->driver->getJobsReady());

		$this->driver->addJob($this->getSerializedJob(), $priority = 1024, $delay = 0, $timeToRun = 60);

		$this->assertEquals(1, $this->driver->getJobsReady());

		$job = $this->driver->getJob();

		$this->assertInstanceOf(Job::class, $job);

		$this->assertEquals(0, $this->driver->getJobsReady());
	}

	/** @test */
	public function it_erases_ready_jobs_for_testing_purposes()
	{
		$this->assertEquals(0, $this->driver->getJobsReady());

		$this->driver->addJob($this->getSerializedJob(), $priority = 1024, $delay = 0, $timeToRun = 60);

		$this->assertEquals(1, $this->driver->getJobsReady());

		$this->driver->erase();

		$this->assertEquals(0, $this->driver->getJobsReady());
	}

	/** @test */
	public function it_erases_buried_jobs_for_testing_purposes()
	{
		$this->assertEquals(0, $this->driver->getJobsBuried());

		$this->driver->addJob($this->getSerializedJob(), $priority = 1024, $delay = 0, $timeToRun = 60);
		$this->driver->getJob()->bury();

		$this->assertEquals(1, $this->driver->getJobsBuried());

		$this->driver->erase();

		$this->assertEquals(0, $this->driver->getJobsBuried());
	}

	private function getSerializedJob()
	{
		return JobSerializer::make(SampleJob::class, ['message' => 'foobar']);
	}
}