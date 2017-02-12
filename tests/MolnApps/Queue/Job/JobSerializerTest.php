<?php

namespace MolnApps\Queue\Job;

use \MolnApps\Queue\Testing\SampleJob;

class JobSerializerTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_serializes_a_job()
	{
		$result = JobSerializer::make(SampleJob::class, ['message' => 'Lorem ipsum dolor sit amet']);

		$this->assertEquals(
			'{"jobIdentifier":"MolnApps\\\\Queue\\\\Testing\\\\SampleJob","data":{"message":"Lorem ipsum dolor sit amet"}}',
			$result
		);
	}

	/** @test */
	public function it_unserializes_a_job_and_returns_qualified_instance()
	{
		$job = JobSerializer::take('{"jobIdentifier":"MolnApps\\\\Queue\\\\Testing\\\\SampleJob","data":{"message":"Lorem ipsum dolor sit amet"}}');

		$this->assertInstanceOf(SampleJob::class, $job);
	}
}