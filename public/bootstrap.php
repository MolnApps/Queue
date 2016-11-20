<?php

require __DIR__ . "/../vendor/autoload.php";

use \MolnApps\Queue\QueueManager;

$queue = QueueManager::make([
	'driver' => 'beanstalk',
	'host' => '127.0.0.1',
	'name' => 'sample',
	'reserve' => 10,
])->getQueue();