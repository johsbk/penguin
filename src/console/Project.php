<?php

namespace penguin\console;

class Project
{
    private $app;
    public function __construct($project)
    {
        $this->app = new Silly\Application();
        $this->app->command('runjobs',function (OutputInterface $output) { $this->runJobs($output); });
        $this->app->command('runfailedjobs', function (OutputInterface $output) { $this->runFailedJobs($output);});
        $project->initDatabase();
    }
    public function run()
    {
        $this->app->run();
    }
    private function runJobs(OutputInterface $output)
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
    private function runFailedJobs(OutputInterface $output)
    {
        $jq = \penguin\jobs\FailedJobQueue::getInstance();
        $output->writeln("starting jobs");
        $jq->runJobs();
    }
}
