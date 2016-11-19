<?php

namespace MolnApps\Queue\Job;

class JobSerializer
{
	public static function take($serializedJob)
	{
		if ( ! $serializedJob) {
			return;
		}

		$unserialized = json_decode($serializedJob);
		
		$object = JobFactory::createJob($unserialized->jobIdentifier);

		$object->setData($unserialized->data);
		$object->setIdentifier($unserialized->jobIdentifier);
		
		return $object;
	}

	public static function make($jobIdentifier, $data)
	{
		return json_encode([
			'jobIdentifier' => $jobIdentifier,
			'data' => $data
		]);
	}
}