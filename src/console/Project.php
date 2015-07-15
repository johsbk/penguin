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
			$pid = file_get_contents($pidfile);
			$found = false;
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			    echo 'This is a server using Windows!';
			    exit;
			} else {
				exec("ps $pid",$data);
				$found = count($data) > 1;
			}
			if ($found) {
				echo "Process already running\n";

				exit;
			}
		} 
		file_put_contents($pidfile, getmypid());
		register_shutdown_function(function () use ($pidfile) {
			echo "deleting pid file\n";
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