<?php

namespace MolnApps\Queue\Testing;

use \MolnApps\Queue\Job\JobFactory;
use \MolnApps\Queue\Job\Touchable;

class TouchJobTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_can_be_instantiated()
	{
		$instance = JobFactory::createJob(TouchJob::class);

		$this->assertNotNull($instance);
	}

	/** @test */
	public function it_touches_the_driver_job()
	{
		$touchableStub = $this->prophesize(Touchable::class);
		$touchableStub->touch()->shouldBeCalledTimes(5);

		$instance = JobFactory::createJob(TouchJob::class);
		$instance->setTouchable($touchableStub->reveal());

		$instance->perform();
	}
}