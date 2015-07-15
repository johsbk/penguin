<?php
namespace penguin\console;
class Project {
	function __construct($project) {
		$project->initDatabase();
	}
	function run() {
		foreach ($_SERVER['argv'] as $arg) {
			switch($arg) {
				case 'runjobs':
					$this->runJobs();
					break;
				case 'runfailedjobs':
					$this->runFailedJobs();
					break;
			}
		}
	}
	private function runJobs() {
		$pidfile = 'runjobs.pid';
		if (file_exists($pidfile)) {
			echo "$pidfile exists already";
		} 
		file_put_contents($pidfile, getmypid());
		register_shutdown_function(function () use ($pidfile) {
			unlink($pidfile);
		});  
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