<?php
/**
 * High-level consumer
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/11/21
 * Time: 下午4:10
 */

namespace kafka\lib;

class Consumer extends BaseConsumer
{

    public function __construct($broker, int $sessionTimeout = 30000)
    {
        parent::__construct($broker, $sessionTimeout);
    }

    /**
     * @param $callback
     * @throws \Exception
     */
    public function start($callback)
    {
        if (empty($this->groupId) || empty($this->topic)) {
            throw new \Exception("please set groupId and topic to this consumer");
        }
        $this->setConf('group.id', $this->groupId);
        $topicConf = new \Rdkafka\TopicConf();
        $topicConf->set('auto.offset.reset', $this->offsetAutoReset);
        $this->conf->setDefaultTopicConf($topicConf);

        $this->rk = new \Rdkafka\KafkaConsumer($this->conf);

        $this->lastCommitTime = $this->getTime();

        $this->lastWatchTime = 0;

        $this->rk->subscribe([$this->topic]);

        while (self::$running) {
            $this->consume($callback);
            pcntl_signal_dispatch();
        }
    }


    private function consume($callback)
    {
        $msg = $this->rk->consume($this->consumeTimeout);
        if(empty($msg)) {
            call_user_func($this->nullHandler, $msg);
        }elseif ($msg->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
            call_user_func($callback, $msg);
        } elseif ($msg->err === RD_KAFKA_RESP_ERR__TIMED_OUT) {
            call_user_func($this->timeoutHandler, $msg);
        } elseif ($msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            call_user_func($this->eofHandler, $msg);
        } else {
            throw new \Exception($msg->errstr(), $msg->err);
        }
    }

}