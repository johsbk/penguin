<?php
namespace penguin\jobs;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use penguin\patterns\Singleton;
class JobQueue extends Singleton {
	protected $queue_name = 'job_queue';
	private $conn;
	private $channel;
	/**
	 * @var $queue_name String
	 */
	function __construct() {
		$this->conn = new AMQPConnection('localhost',5672,'guest','guest');
		$this->channel = $this->conn->channel();
		$this->channel->queue_declare($this->queue_name,false,true,false,false);
	}
	/**
	 * @var $job Job
	 */
	function add($job) {
		$msg = new AMQPMessage(serialize($job),array('delivery_mode'=>2));
		$this->channel->basic_publish($msg,'',$this->queue_name);

	}
	function runJobs() {
		$cb = function ($msg) {
			$obj = unserialize($msg->body);
			try {
				$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
				if ($obj instanceof Job)
					$obj->run();
			} catch (\Exception $e) {
				echo 'Exception caught: '.$e->getMessage();
				$fjq = FailedJobQueue::getInstance();
				$fjq->add($obj);
			}
		};
		$this->channel->basic_qos(null,1,null);
		$this->channel->basic_consume($this->queue_name,'',false,false,false,false,$cb);
		while(count($this->channel->callbacks)) {
			$this->channel->wait();
		}
	}
	function __destruct() {
		$this->channel->close();
		$this->conn->close();
	}
}