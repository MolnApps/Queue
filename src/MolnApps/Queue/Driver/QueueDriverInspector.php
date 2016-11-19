<?php

namespace MolnApps\Queue\Driver;

interface QueueDriverInspector
{
	public function getJobsReady();
	public function getJobsBuried();
}