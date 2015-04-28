<?php
namespace penguin\jobs;
class FailedJobQueue extends JobQueue {
	protected $queue_name = 'failed_job_queue';
}