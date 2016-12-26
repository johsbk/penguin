<?php

namespace penguin\console;
use Symfony\Component\Console\Output\OutputInterface;
class Project
{
    private $app;
    public function __construct($project)
    {
        $this->app = new \Silly\Edition\PhpDi\Application();
        $this->app->command('runjobs','commands\RunJobs');
        $this->app->command('runfailedjobs', 'commands\RunFailedJobs');
        $project->initDatabase();
    }
    public function run()
    {
        $this->app->run();
    }
}
