<?php

namespace MolnApps\Queue\Testing;

class LongJob extends AbstractJob
{
	public function perform()
	{
		var_dump('start long job');
		for ($i = 0; $i < 2; $i++) {
			var_dump($i);
			sleep(1);
		}
		var_dump('end long job');
	}
}