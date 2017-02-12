<?php

namespace MolnApps\Queue\Driver;

use \MolnApps\Queue\Job\JobSerializer;
use \MolnApps\Queue\Job\Job;

use \MolnApps\Queue\Testing\SampleJob;
use \MolnApps\Queue\Testing\StopWorkerJob;
use \MolnApps\Queue\Testing\TouchJob;

class AbstractDriverJobTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_can_perform_a_job()
	{
		$stub = $this->createStub(SampleJob::class);

		$stub->perform();
	}

	/** @test */
	public function it_determines_if_a_job_should_stop_the_worker()
	{
		$stub = $this->createStub(StopWorkerJob::class);
		$this->assertTrue($stub->stopsWorker());

		$stub = $this->createStub(SampleJob::class);
		$this->assertFalse($stub->stopsWorker());
	}

	/** @test */
	public function it_returns_the_job_identifier()
	{
		$stub = $this->createStub(SampleJob::class);

		$this->assertEquals(SampleJob::class, $stub->getIdentifier());
	}

	/** @test */
	public function it_records_itself_as_touchable()
	{
		$stub = $this->createStub(TouchJob::class);

		$stub->perform();
	}

	private function createStub($jobIdentifier)
	{
		$stub = $this->getMockForAbstractClass(AbstractDriverJob::class);

		$stub->expects($this->any())
			->method('delete')
			->willReturn(null);

		$stub->expects($this->any())
			->method('bury')
			->willReturn(null);

		$stub->expects($this->any())
			->method('getSerializedJob')
			->willReturn(JobSerializer::make($jobIdentifier, ['message' => 'foobar']));

		return $stub;
	}
}