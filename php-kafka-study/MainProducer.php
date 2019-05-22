<?php
/**
 * mainProducer
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/11/21
 * Time: 下午5:25
 */

namespace kafka;

use kafka\lib\Producer;

class MainProducer extends Producer
{
    public function __construct($brokers)
    {
        parent::__construct($brokers);
    }

    public function produce($topic, $payload, $key = '', $partition = RD_KAFKA_PARTITION_UA)
    {
        if (php_sapi_name() == "cli") {
            return $this->doProduceNotKeepAlive($topic, $payload, $key, $partition);
        } else {
            return $this->doProduce($topic, $payload, $key, $partition);
        }
    }

    protected function callbackOnError($rk, $err, $reason)
    {
        echo "Kafka error: " . rd_kafka_err2str($err) . " (reason: {$reason})\r\n";
    }

    protected function callbackOnDrMsgFail($rk, $message)
    {
        echo $message . "\r\n";
    }

    protected function callbackOnDrMsgSuccess($rk, $message)
    {
    }

}