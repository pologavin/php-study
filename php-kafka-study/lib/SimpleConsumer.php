<?php
/**
 * Simple (Low-level) consumer
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/9
 * Time: 下午2:18
 */

namespace kafka\lib;

use RdKafka\Queue;

class SimpleConsumer extends BaseConsumer
{

    public function __construct($broker, int $sessionTimeout = 30000)
    {
        parent::__construct($broker, $sessionTimeout);
        $this->topicConf = new \Rdkafka\TopicConf();
        $this->topicConf->set('auto.offset.reset', $this->offsetAutoReset);
        $this->topicConf->set('auto.commit.enable', true);
        $this->topicConf->set('offset.store.method', 'file');
        $this->topicConf->set('offset.store.path', ROOT . DIRECTORY_SEPARATOR . 'logs/log-offset.log');
        $this->conf->setDefaultTopicConf($this->topicConf);

        $this->setOffset(RD_KAFKA_OFFSET_STORED);

        $this->rk = new \RdKafka\Consumer($this->conf);
        $this->rkTopic = $this->rk->newTopic($this->topic, $this->topicConf);
    }

    /**
     * @param $callback
     * @throws \Exception
     */
    public function start($callback)
    {
        if (empty($this->groupId) || empty($this->topic) || empty($this->partitions)) {
            throw new \Exception("please set groupId and topic with partition to this consumer");
        }

        while (self::$running) {
            foreach ($this->partitions as $partition) {
                $this->offset = empty($this->offsets[$partition]) ? $this->offset : $this->offsets[$partition];
                $this->setCurrentPartition($partition);
                if (!in_array($partition, $this->consumedPartitions)) {
                    $this->rkTopic->consumeStart($partition, $this->offset);
                    array_push($this->consumedPartitions, $partition);
                }
                $this->consume($callback);
            }
            pcntl_signal_dispatch();
        }
    }

    /**
     * @param $callback
     * @throws \Exception
     */
    public function queueStart($callback)
    {
        if (empty($this->groupId) || empty($this->topics) || empty($this->partitions)) {
            throw new \Exception("please set groupId and topics with partition to this consumer");
        }

        $queue = $this->rk->newQueue();
        foreach ($this->topics as $topicName => $partitions) {
            if (empty($topicName)) {
                continue;
            }
            $topic = $this->rk->newTopic($topicName);
            if (empty($this->partitions)) {
                $topic->consumeQueueStart(0, RD_KAFKA_OFFSET_BEGINNING, $queue);
            }
            foreach ($partitions as $partition) {
                $this->offset = empty($this->offsets[$partition]) ? $this->offset : $this->offsets[$partition];
                $this->setCurrentPartition($partition);
                if (!in_array($partition, $this->consumedPartitions)) {
                    $this->rkTopic->consumeQueueStart($partition, $this->offset, $queue);
                    array_push($this->consumedPartitions, $partition);
                }
            }
        }
        while (self::$running && $this->eofCount < 2) {
            $this->consume($callback, $queue);
            pcntl_signal_dispatch();
        }

    }

    /**
     * @param $callback
     * @param Queue|bool $queue
     * @throws \Exception
     */
    public function consume($callback, $queue = false)
    {
        if ($queue !== false) {
            // queue consume
            $msg = $queue->consume($this->consumeTimeout);
        } else {
            $msg = $this->rkTopic->consume($this->currentPartition, $this->consumeTimeout);
        }
        if (empty($msg)) {
            call_user_func($this->nullHandler, $msg);
        } elseif ($msg->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
            call_user_func($callback, $msg);
        } elseif ($msg->err === RD_KAFKA_RESP_ERR__TIMED_OUT) {
            call_user_func($this->timeoutHandler, $msg);
        } elseif ($msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            call_user_func($this->eofHandler, $msg);
            $this->eofCount++;
        } else {
            throw new \Exception($msg->errstr(), $msg->err);
        }
    }
}