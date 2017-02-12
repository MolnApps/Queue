<?php

namespace MolnApps\Queue\Driver;

interface QueueDriver
{
	public function addJob($serializedJob, $priority, $delay, $timeToRun);
	public function getJob();
}