<?php

namespace MolnApps\Queue\Testing;

class TouchJob extends AbstractJob
{
	public function perform()
	{
		for ($i = 0; $i < 5; $i++) {
			$this->performHeavyOperation();
			$this->touch();
		}
	}

	private function performHeavyOperation()
	{
		// Doing noghting
	}
}