<?php

require "bootstrap.php";

use \MolnApps\Queue\Worker\Monitor;
use \MolnApps\Queue\Worker\BaseWorker;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

$logger = new Logger('sample');
$logger->pushHandler(new StreamHandler('/var/www/log/test.log', Logger::DEBUG));

$monitor = new Monitor();
$monitor->setLogger($logger);

$worker = new BaseWorker($queue, $monitor, $_SERVER['WORKER_ID']);
$worker->run();