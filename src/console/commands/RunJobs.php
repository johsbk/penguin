<?php
namespace penguin\console\commands;
use Symfony\Component\Console\Output\OutputInterface;

class RunJobs {
	public function __invoke(OutputInterface $output)
    {
        $pidfile = 'runjobs.pid';
        if (file_exists($pidfile)) {
            $pid = file_get_contents($pidfile);
            $found = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("tasklist /fi \"PID eq $pid", $data);
                $found = count($data) > 1;
            } else {
                exec("ps $pid", $data);
                $found = count($data) > 1;
            }
            if ($found) {
                $output->writeln("Process already running");

                exit;
            }
        }
        file_put_contents($pidfile, getmypid());
        register_shutdown_function(function () use ($pidfile) {
            $output->writeln("deleting pid file");
            unlink($pidfile);
        });
        $jq = \penguin\jobs\JobQueue::getInstance();
        $output->writeln("starting jobs");
        $jq->runJobs();
    }
}