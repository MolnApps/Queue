<?php

namespace MolnApps\Queue\Testing;

use \MolnApps\Queue\Job\Job;
use \MolnApps\Queue\Job\Touchable;

abstract class AbstractJob implements Job
{
	private $data;
	private $identifier;

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}

	public function getIdentifier()
	{
		return $this->identifier ?: get_class();
	}

	public function setTouchable(Touchable $touchable)
	{
		$this->touchable = $touchable;
	}
	
	protected function touch()
	{
		$this->touchable->touch();
	}

	public function perform()
	{
		//var_dump('perform job ' . $this->getIdentifier());
	}
}