<?php

namespace penguin\jobs;

abstract class Job
{
    public $created_at;
    private $retries=0;
    abstract public function run();

    public function create()
    {
        $this->created_at = new \DateTime();
        $jq = JobQueue::getInstance();
        $jq->add($this);
    }
    public function retry() {
    	if ($this->retries++>5) {
    		RetryJobQueue::getInstance()->add($this);
    	} else {
    		FailedJobQueue::getInstance()->add($this);
    	}
    }
}
