<?php

namespace MolnApps\Queue\Testing;

use \MolnApps\Queue\Job\JobStopsWorker;

class StopWorkerJob extends AbstractJob implements JobStopsWorker
{
	public function shouldStopWorker()
	{
		return true;
	}
}