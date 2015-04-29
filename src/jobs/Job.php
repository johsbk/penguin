<?php
namespace penguin\jobs;
abstract class Job {
	public $created_at;
	abstract function run();

	function create() {
		$this->created_at = new \DateTime();
		$jq = JobQueue::getInstance();
		$jq->add($this);
	}
}