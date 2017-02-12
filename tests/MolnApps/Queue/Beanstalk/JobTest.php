<?php

namespace MolnApps\Queue\Beanstalk;

use \Pheanstalk\Pheanstalk;
use \Pheanstalk\Job as PheanstalkJob;

class JobTest extends \PHPUnit_Framework_TestCase
{
	private $pheanstalkJobMock;
	private $pheanstalkProphet;

	/** @before */
	public function setUpStubs()
	{
		$this->pheanstalkJobMock = $this->createMock(PheanstalkJob::class);

		$this->pheanstalkProphet = $this->prophesize(Pheanstalk::class);
	}

	/** @test */
	public function it_can_be_instantiated()
	{
		$job = new Job($this->pheanstalkProphet->reveal(), $this->pheanstalkJobMock);

		$this->assertNotNull($job);
	}

	/** @test */
	public function it_can_be_deleted()
	{
		$this->pheanstalkProphet->delete($this->pheanstalkJobMock)->shouldBeCalled();

		$job = new Job($this->pheanstalkProphet->reveal(), $this->pheanstalkJobMock);

		$job->delete();
	}

	/** @test */
	public function it_can_be_buried()
	{
		$this->pheanstalkProphet->bury($this->pheanstalkJobMock)->shouldBeCalled();

		$job = new Job($this->pheanstalkProphet->reveal(), $this->pheanstalkJobMock);

		$job->bury();
	}

	/** @test */
	public function it_can_be_touched()
	{
		$this->pheanstalkProphet->touch($this->pheanstalkJobMock)->shouldBeCalled();

		$job = new Job($this->pheanstalkProphet->reveal(), $this->pheanstalkJobMock);

		$this->assertInstanceOf(\MolnApps\Queue\Job\Touchable::class, $job);

		$job->touch();
	}
}