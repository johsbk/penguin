<?php

namespace penguin\console;
class Project
{
    private $app;
    public function __construct($project)
    {
        $this->app = new \Silly\Edition\PhpDi\Application();
        $this->app->command('runjobs','penguin\console\commands\RunJobs');
        $this->app->command('runfailedjobs', 'penguin\console\commands\RunFailedJobs');
        $project->initDatabase();
    }
    public function command($cmd,$ref) {
        $this->app->command($cmd,$ref);
    }
    public function run()
    {
        $this->app->run();
    }
}
