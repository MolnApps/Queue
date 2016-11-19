<?php

namespace MolnApps\Queue\Driver;

interface QueueDriver
{
	public function addJob($serializedJob);
	public function getJob();
}