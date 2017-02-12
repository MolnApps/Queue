<?php

namespace MolnApps\Queue;

use \MolnApps\Queue\Driver\QueueDriver;

class QueueTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_can_be_instantiated()
	{
		$instance = new Queue;

		$this->assertNotNull($instance);
	}

	/** @test */
	public function it_accepts_a_queue_driver()
	{
		$instance = new Queue;

		$driver = $this->createMock(QueueDriver::class);

		$instance->setDriver($driver);

		$this->assertEquals($driver, $instance->getDriver());
	}

	/** @test */
	public function it_throws_if_no_queue_driver_is_set()
	{
		$this->setExpectedException(\Exception::class, 'Please provide a driver through the ::setDriver() method');

		$instance = new Queue;

		$instance->getDriver();
	}

	/** @test */
	public function it_accepts_custom_time_to_run()
	{
		$instance = new Queue;

		$instance->withTimeToRun(180);

		$this->assertEquals(180, $instance->getTimeToRun());
	}

	/** @test */
	public function it_provides_default_time_to_run()
	{
		$instance = new Queue;

		$this->assertEquals(60, $instance->getTimeToRun());
	}

	/** @test */
	public function it_accepts_custom_delay()
	{
		$instance = new Queue;

		$instance->withDelay(12);

		$this->assertEquals(12, $instance->getDelay());
	}

	/** @test */
	public function it_provides_default_delay()
	{
		$instance = new Queue;

		$this->assertEquals(0, $instance->getDelay());
	}

	/** @test */
	public function it_accepts_custom_priority()
	{
		$instance = new Queue;

		$instance->withPriority(0);

		$this->assertEquals(0, $instance->getPriority());
	}

	/** @test */
	public function it_provides_default_priority()
	{
		$instance = new Queue;

		$this->assertEquals(1024, $instance->getPriority());
	}

	/** @test */
	public function it_adds_a_job_to_a_queue_with_default_arguments()
	{
		$instance = new Queue;

		$driver = $this->prophesize(QueueDriver::class);
		$driver->addJob(\Prophecy\Argument::any(), 1024, 0, 60)->shouldBeCalled();

		$instance->setDriver($driver->reveal());

		$instance
			->addJob(SampleJob::class, ['message' => 'foobar']);
	}

	/** @test */
	public function it_adds_a_job_to_a_queue_with_custom_arguments()
	{
		$instance = new Queue;

		$driver = $this->prophesize(QueueDriver::class);
		$driver->addJob(\Prophecy\Argument::any(), 0, 10, 180)->shouldBeCalled();

		$instance->setDriver($driver->reveal());

		$instance
			->withTimeToRun(180)
			->withDelay(10)
			->withPriority(0)
			->addJob(SampleJob::class, ['message' => 'foobar']);

		// Assert default values are reset
		$this->assertEquals(60, $instance->getTimeToRun());
		$this->assertEquals(0, $instance->getDelay());
		$this->assertEquals(1024, $instance->getPriority());
	}

	/** @test */
	public function it_retrieves_a_job_from_driver()
	{
		$instance = new Queue;

		$driver = $this->prophesize(QueueDriver::class);
		$driver->getJob()->willReturn('aJobInstance')->shouldBeCalled();

		$instance->setDriver($driver->reveal());

		$job = $instance->getJob();

		$this->assertEquals('aJobInstance', $job);
	}
}