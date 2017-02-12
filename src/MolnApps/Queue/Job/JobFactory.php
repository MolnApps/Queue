<?php

namespace MolnApps\Queue\Job;

class JobFactory
{
	public static function createJob($jobName)
	{
		if ( ! class_exists($jobName)) {
			throw new \Exception('Could not create job ' . $jobName);
		}

		$job = new $jobName;

		if ( ! $job instanceof Job) {
			throw new \Exception('Job ' . $jobName . ' must implements interface ' . Job::class);
		}

		return $job;
	}
}