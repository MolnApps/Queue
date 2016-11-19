<?php

namespace MolnApps\Queue\Job;

interface Job
{
	public function setData($data);
	public function perform();
	public function getIdentifier();
}