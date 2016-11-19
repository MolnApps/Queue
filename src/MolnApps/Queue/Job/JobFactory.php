<?php

namespace MolnApps\Queue\Job;

class JobFactory
{
	public static function createJob($jobName)
	{
		$job = new $jobName;

		if ( ! $job instanceof Job) {
			throw new \Exception('Job ' . $jobName . ' must implements interface ' . Job::class);
		}

		return $job;
	}
}