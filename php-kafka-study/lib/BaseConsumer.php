<?php
/**
 * base consumer
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/5/9
 * Time: 下午2:32
 */

namespace kafka\lib;

use RdKafka\ConsumerTopic;
use RdKafka\TopicConf;

class BaseConsumer
{
    const SMALLEST = 'smallest';
    const LARGEST = 'largest';

    protected $broker;
    protected $brokerVersionFallback = '0.11.0.2';
    protected $groupId;
    protected $topic;
    /**
     * queue 消费topics组
     * @var array eg:['topic_key' => [partitionId1,partitionId2...]]
     */
    protected $topics = array();
    protected $maxMessage;
    protected $commitInterval;
    protected $watchInterval;
    protected $consumeTimeout;
    /**
     * @var \Rdkafka\Conf
     */
    protected $conf;
    /**
     * @var \Rdkafka\TopicConf
     */
    protected $topicConf;

    /**
     * @var \RdKafka
     */
    protected $partitions = array();
    protected $offsets = array();
    protected $currentPartition = RD_KAFKA_MSG_PARTITIONER_RANDOM;
    protected $offset = RD_KAFKA_OFFSET_BEGINNING;
    protected $consumedPartitions = array();

    /**
     * @var \RdKafka\KafkaConsumer
     */
    protected $rk;
    /**
     * @var \RdKafka\ConsumerTopic
     */
    protected $rkTopic;
    protected $lastCommitTime;
    protected $lastWatchTime;
    protected $errHandler;
    protected $eofHandler;
    protected $timeoutHandler;
    protected $nullHandler;
    protected $offsetAutoReset;
    protected static $running = true;
    protected $eofCount = 0;

    public function __construct($broker, $sessionTimeout = 30000)
    {
        $this->broker = $broker;
        $this->topic = null;
        $this->maxMessage = 32;
        $this->commitInterval = 500;
        $this->watchInterval = 10000;
        $this->consumeTimeout = 1000;
        $this->errHandler = function ($msg) {
            printf("partition: %d , err: %s \n", $msg->partition, $msg->errstr());
        };
        $this->eofHandler = function ($msg) {
            printf("partition: %d , no more messages; will wait for more \n", $msg->partition);
        };
        $this->timeoutHandler = function ($msg) {
            printf("partition: %d , kafka response timeout\n", $msg->partition);
        };
        $this->nullHandler = function () {
            printf("msg return NULL\n");
        };

        $this->offsetAutoReset = self::SMALLEST;

        $this->conf = new \Rdkafka\Conf();
        $this->conf->set('broker.version.fallback', $this->brokerVersionFallback);
        $this->conf->set('queued.max.messages.kbytes', 1024);
        $this->conf->set('topic.metadata.refresh.interval.ms', 60000);
        $this->conf->set('fetch.message.max.bytes', 1048576);
        $this->conf->set('metadata.broker.list', $this->broker);
    }

    public function setConf($attribute, $value)
    {
        $this->conf->set($attribute, $value);
    }

    /**
     * Set consumer group Id, this value must be set
     *
     * @var String $groupId consumer group Id
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Set topic, this value must be set
     *
     * @var String $topic topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * If partitions > 1, it forces consumers to switch to other
     * partitons when max message is reached, or other partitons
     * will be starved.
     *
     * @var Int $maxMessage max message number, defaults to 32
     */
    public function setMaxMessage($maxMessage)
    {
        $this->maxMessage = $maxMessage;
    }

    /**
     * Set offset auto commit interval.
     *
     * @var Ind $commitInterval the unit is milliseconds, defaults to 500
     */
    public function setCommitInterval($commitInterval)
    {
        $this->commitInterval = $commitInterval;
    }

    /**
     * Set time interval to check rebalance. Rebalance is triggered
     * when the number of partition or consumer changes.
     *
     * @var Int $watchInterval the unit is milliseconds, defaults to 10000
     */
    public function setWatchInterval($watchInterval)
    {
        $this->watchInterval = $watchInterval;
    }

    /**
     * Set kafka request timeout.
     *
     * @var Int $consumeTimeout the unit is milliseconds, defaults to 1000
     */
    public function setConsumeTimeout($consumeTimeout)
    {
        $this->consumeTimeout = $consumeTimeout;
    }

    /**
     * Set client id is used to identify consumers
     *
     * @var String $clientId client id, defaults to "default"
     */
    public function setClientId($clientId)
    {
        $this->consumerId = $this->consumerIdPrefix . '-' . $clientId;
    }

    /**
     * Set a callback function is used to handle error
     *
     * @var Function $errorHandler error handle functioin
     */
    public function setErrHandler($errHandler)
    {
        $this->errHandler = $errHandler;
    }

    /**
     * Set a callback function is used to handle when consumer offset
     * reach the end of partition
     *
     * @var Function $eofHandler eof handle function
     */
    public function setEofHandler($eofHandler)
    {
        $this->eofHandler = $eofHandler;
    }

    public function setNullHandler($nullHandler)
    {
        $this->nullHandler = $nullHandler;
    }

    /**
     * Set offset auto reset rule. Consumer can choose whether to fetch
     * the oldest or the lastest message when offset isn't present in
     * zookeeper or is out of range.
     *
     * @var Int $autoReset smallest or largest, defaults to samllest
     */
    public function setOffsetAutoReset($autoReset)
    {
        if ($autoReset === self::SMALLEST || $autoReset === self::LARGEST) {
            $this->offsetAutoReset = $autoReset;
        } else {
            throw new Exception ("invalid offset auto reset argument: you should set
                smallest or largest");
        }
    }

    public function setPartitions(array $partitions)
    {
        $this->partitions = $partitions;
    }

    public function addPartition($partition)
    {
       array_push($this->partitions, $partition);
       $this->partitions = array_unique($this->partitions);
    }

    public function setCurrentPartition($partition)
    {
        $this->currentPartition = $partition;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    //get current time
    protected function getTime()
    {
        return microtime(true) * 1000;
    }

    /**
     * stop consuming messages
     */
    public static function stop()
    {
        self::$running = false;
    }

}