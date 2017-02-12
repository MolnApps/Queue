<?php

namespace MolnApps\Queue\Job;

use \MolnApps\Queue\Testing\SampleJob;

class JobFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_creates_an_existing_job()
	{
		$job = JobFactory::createJob(SampleJob::class);

		$this->assertInstanceOf(SampleJob::class, $job);
	}

	/** @test */
	public function it_throws_an_exception_if_job_does_not_exists()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Could not create job Foo\Bar'
		);

		$job = JobFactory::createJob('Foo\Bar');
	}

	/** @test */
	public function it_throws_an_exception_if_class_does_not_implement_job_interface()
	{
		$this->setExpectedException(
			\Exception::class, 
			'Job MolnApps\Queue\Job\JobFactory must implements interface MolnApps\Queue\Job\Job'
		);
		
		$job = JobFactory::createJob(JobFactory::class);
	}
}