<?php
namespace penguin\jobs;
use Analog\Analog;
class FailedJobQueue extends JobQueue {
	protected $queue_name = 'failed_job_queue';
	function runJobs() {
		$cb = function ($msg) {
			$obj = unserialize($msg->body);
			try {
				echo date('Y-m-d H:i:s').': Running '.get_class($obj)."...";
				if ($obj instanceof Job)
					$obj->run();

				$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			} catch (\Exception $e) {				
				Analog::log($e);
				echo "Exception caught: ".$e->getMessage()."\n";
				exit();
				echo "done\n";
			}
		};
		$this->channel->basic_qos(null,1,null);
		$this->channel->basic_consume($this->queue_name,'',false,false,false,false,$cb);
		while(count($this->channel->callbacks)) {
			$this->channel->wait();
		}
	}
}