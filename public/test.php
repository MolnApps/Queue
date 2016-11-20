<?php

require "bootstrap.php";

use \MolnApps\Queue\Testing\SampleJob;
use \MolnApps\Queue\Testing\ThrowsExceptionJob;

$queue->addJob(SampleJob::class, []);
$queue->addJob(SampleJob::class, []);
$queue->addJob(ThrowsExceptionJob::class, []);
$queue->addJob(SampleJob::class, []);
$queue->addJob(SampleJob::class, []);