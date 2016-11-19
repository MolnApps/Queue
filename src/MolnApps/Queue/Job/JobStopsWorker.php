<?php

namespace MolnApps\Queue\Job;

interface JobStopsWorker
{
	public function shouldStopWorker();
}