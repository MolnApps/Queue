<?php

namespace MolnApps\Queue\Driver;

class SyncDriver implements QueueDriver
{
	public function addJob($serializedJob)
	{
		// Do nothing
	}

	public function getJob()
	{
		// Do nothing
	}
}