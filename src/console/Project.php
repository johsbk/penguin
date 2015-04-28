<?php
namespace penguin\console;
class Project {
	function __construct($project) {
		$project->initDatabase();
	}
	function run() {
		print_r($_SERVER['argv']);
	}
	private function runJobs() {		
		$jq = \penguin\jobs\JobQueue::getInstance();
		echo "starting jobs\n";
		$jq->runJobs();
	}
	private function runFailedJobs() {		
		$jq = \penguin\jobs\FailedJobQueue::getInstance();
		echo "starting jobs\n";
		$jq->runJobs();
	}
}