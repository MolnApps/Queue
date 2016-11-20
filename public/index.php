<?php

require "../vendor/autoload.php";

use \MolnApps\Queue\Worker\Monitor;

$monitor = new Monitor('sample');
$heartbeat = $monitor->getLastHeartbeat(1);

$rows = [
	'Worker id' => $heartbeat->workerId,
	'Worker hash' => $heartbeat->workerHash,
	'Status' => $heartbeat->status,
	'Timestamp' => $heartbeat->timestamp,
	'Time limit' => $heartbeat->timeLimit / 60 . ' minutes',
	'Start time' => gmdate('Y-m-d H:i:s', $heartbeat->startTime),
	'End time' => gmdate('Y-m-d H:i:s', $heartbeat->endTime),
	'Jobs count' => $heartbeat->jobsCount,
	'Errors count' => $heartbeat->errorsCount,
];

echo '<h2>Worker heartbeat</h2>';
echo '<table>';
foreach ($rows as $label => $value) {
	echo '<tr>';
	echo '<td><strong>' . $label . '</strong>:</td><td>' . $value . '</td>';
	echo '</tr>';
}
echo '</table>';

echo '<h2>Last 25 logs</h2>';
$contents = file_get_contents('../log/test.log');
$lines = explode("\n", $contents);
$slice = array_slice($lines, count($lines) - 25, 25);
foreach ($slice as $line) {
	echo $line . '<br/>';
}