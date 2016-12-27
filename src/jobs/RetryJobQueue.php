<?php

namespace penguin\jobs;

use Analog\Analog;

class RetryJobQueue extends JobQueue
{
    protected $queue_name = 'retry_job_queue';
    protected $exchange_name = 'retry_job_exchange';
    protected  function getQueueArguments() {
        return [
        	"x-dead-letter-exchange" => 'job_exchange',
    		"x-message-ttl" => 3600000
        ];
    }
    public function runJobs()
    {
        // do nothing
    }
}
