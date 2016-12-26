<?php
namespace penguin\console\commands;
use Symfony\Component\Console\Output\OutputInterface;

class RunFailedJobs {

    public function __invoke(OutputInterface $output)
    {
        $jq = \penguin\jobs\FailedJobQueue::getInstance();
        $output->writeln("starting jobs");
        $jq->runJobs();
    }
}