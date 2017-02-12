<?php

namespace MolnApps\Queue\Job;

interface Job
{
	public function setIdentifier($identifier);
	public function setData($data);
	public function setTouchable(Touchable $touchable);

	public function getIdentifier();

	public function perform();
}