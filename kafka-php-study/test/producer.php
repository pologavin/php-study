<?php
/**
 * producer
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/14
 * Time: 上午11:22
 */

namespace Kafka\test;

use Kafka_php\conf\Kafka;
use Kafka_php\conf\Keys;
use Kafka\Exception;
use Kafka\Producer;
use Kafka\ProducerConfig;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try {

    $config = ProducerConfig::getInstance();
    $config->setMetadataRefreshIntervalMs(10000);
    $config->setMetadataBrokerList(Kafka::BROKER);
    $config->setBrokerVersion(Kafka::VERSION);
    $config->setRequiredAck(1);
    $config->setIsAsyn(false);
    $config->setProduceInterval(500);

    $producer = new Producer(function (){
        return array(
            array(
                'topic' => Keys::TOPIC_TEST_DEMO,
                'value' => 'test....message.',
                'key' => Keys::GROUP_DEMO,
            ),
        );
    });

    $producer->success(function($result) {
        var_dump($result);
    });
    $producer->error(function($errorCode) {
        var_dump($errorCode);
    });
    $producer->send(true);

} catch (Exception $e) {
    echo $e->getMessage();
}