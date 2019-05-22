<?php
/**
 * producer
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/11/21
 * Time: 下午4:52
 */

namespace kafka\lib;

abstract class Producer
{
    protected $brokers;
    protected $brokerVersionFallback = '0.11.0.2';
    protected $logLevel = LOG_ERR;
    protected $logger = RD_KAFKA_LOG_SYSLOG;

    protected $messageTimeoutMs = 3000;

    private $lastProduceResult = false;

    /**
     * @var \RdKafka\Producer
     */
    private $rk;
    private $lastConnectTime = 0;

    const MAX_CONNECT_TIME = 60;

    private static $instances;


    protected function __construct($brokers)
    {
        $this->brokers = $brokers;
    }

    /**
     * 单例
     * @return Producer
     */
    public static function getInstance()
    {
        $className = get_called_class();
        $args = func_get_args();
        //若$args中有resource类型的参数,则无法区分同一个类的不同实例
        $key = md5($className . ':' . serialize($args));
        if (!isset(self::$instances[$key])) {
            //PHP_VERSION >= 5.6.0
            self::$instances[$key] = new $className(...$args);
        }
        return self::$instances[$key];
    }

    protected function connect()
    {
        if(!$this->rk || time() - $this->lastConnectTime > self::MAX_CONNECT_TIME) {
            $this->rk = null;
            $rk = $this->getConnect();
            $this->rk = $rk;
            $this->lastConnectTime = time();
        }
    }

    protected function getConnect()
    {
        $kafkaConf = new \RdKafka\Conf();
        $kafkaConf->set('broker.version.fallback', $this->brokerVersionFallback);
        //$kafkaConf->set('socket.blocking.max.ms', 1);
        $kafkaConf->setDrMsgCb(array($this, 'produceDrMsgCallback'));
        $kafkaConf->setErrorCb(array($this, 'produceErrorCallback'));

        $rk = new \RdKafka\Producer($kafkaConf);
        $rk->setLogLevel($this->logLevel);
        $rk->setLogger($this->logger);
        $rk->addBrokers($this->brokers);
        return $rk;
    }


    final public function doProduce($topic, $payload, $key = '', $partition = RD_KAFKA_PARTITION_UA)
    {
        $this->_doProduce($topic, $payload, $key, $partition);
        return $this->lastProduceResult;
    }

    final private function _doProduce($topic, $payload, $key, $partition = RD_KAFKA_PARTITION_UA)
    {
        $this->connect();
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set("message.timeout.ms", $this->messageTimeoutMs);
        $topic = $this->rk->newTopic($topic, $topicConf);

        $partition = $partition === null ? RD_KAFKA_PARTITION_UA : $partition;
        $topic->produce($partition, 0, $payload, $key);

        //阻塞等待，为保证函数结束前已调用DrMsgCb返回发送结果
        //$this->rk->poll($this->messageTimeoutMs + 500);
    }

    final public function doProduceNotKeepAlive($topic, $payload, $key = '', $partition = RD_KAFKA_PARTITION_UA)
    {
        $this->_doProduceNotKeepAlive($topic, $payload, $key, $partition);
        return $this->lastProduceResult;
    }

    final private function _doProduceNotKeepAlive($topic, $payload, $key, $partition = RD_KAFKA_PARTITION_UA)
    {
        $rk = $this->getConnect();
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set("message.timeout.ms", $this->messageTimeoutMs);
        $topic = $rk->newTopic($topic, $topicConf);

        $partition = $partition === null ? RD_KAFKA_PARTITION_UA : $partition;
        $topic->produce($partition, 0, $payload, $key);

        //阻塞等待，为保证函数结束前已调用DrMsgCb返回发送结果
        $rk->poll($this->messageTimeoutMs + 500);
    }


    /**
     * 发送msg结果回调，在整个produce结束后触发
     * @param $rk
     * @param $message
     */
    final public function produceDrMsgCallback($rk, $message)
    {
        if ($message->err) {
            $this->callbackOnDrMsgFail($rk, $message);
            $this->lastProduceResult = false;
        } else {
            $this->callbackOnDrMsgSuccess($rk, $message);
            $this->lastProduceResult = true;
        }
    }

    /**
     * 错误回调方法，可获取produce过程产生的一些错误信息，在produce过程会触发，一次produce有可能会触发多次
     * @param $rk
     * @param $err
     * @param $reason
     */
    final public function produceErrorCallback($rk, $err, $reason)
    {
        $this->callbackOnError($rk, $err, $reason);
    }

    abstract protected function callbackOnError($rk, $err, $reason);

    abstract protected function callbackOnDrMsgSuccess($rk, $message);

    abstract protected function callbackOnDrMsgFail($rk, $message);
}