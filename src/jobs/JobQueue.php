<?php

namespace penguin\jobs;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use penguin\patterns\Singleton;
use Analog\Analog;

class JobQueue extends Singleton
{
    protected $queue_name = 'job_queue';
    protected $exchange_name = 'job_exchange';
    protected $conn;
    protected $channel;
    /**
     * @var String
     */
    public function __construct()
    {
        $this->conn = new AMQPConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->conn->channel();
        $this->channel->exchange_declare($this->exchange_name,'direct');
        $this->channel->queue_declare($this->queue_name, false, true, false, false,false,$this->getQueueArguments());
        $this->channel->queue_bind($this->queue_name,$this->exchange_name);
    }

    protected  function getQueueArguments() {
        return null;
    }
    /**
     * @var Job
     */
    public function add($job)
    {
        $msg = new AMQPMessage(serialize($job), array('delivery_mode' => 2));
        $this->channel->basic_publish($msg, '', $this->exchange_name);
    }
    public function runJobs()
    {
        $cb = function ($msg) {
            $obj = unserialize($msg->body);
            echo date('Y-m-d H:i:s').': Running '.get_class($obj).'...';
            try {
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                if ($obj instanceof Job) {
                    $obj->run();
                }
            } catch (\Exception $e) {
                Analog::log($e);
                echo 'Exception caught: '.$e->getMessage()."\n";
                $fjq = FailedJobQueue::getInstance();
                $fjq->add($obj);
            }
            echo "done\n";
        };
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queue_name, '', false, false, false, false, $cb);
        try {
            while (count($this->channel->callbacks)) {
                $this->channel->wait(null, false, 240);
            }
        } catch (AMQPTimeoutException $e) {
            echo 'timing out. finished running jobs';
        }
    }
}
