<?php
namespace penguin\jobs;
abstract class Job {
	public $created_at = new \DateTime();
	abstract function run();

	function create() {
		$jq = JobQueue::getInstance();
		$jq->add($this);
	}
}