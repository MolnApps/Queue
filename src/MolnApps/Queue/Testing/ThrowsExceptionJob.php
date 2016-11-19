<?php

namespace MolnApps\Queue\Testing;

class ThrowsExceptionJob extends AbstractJob
{
	public function perform()
	{
		throw new \Exception('Could not perform the task');
	}
}