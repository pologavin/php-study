<?php
/**
 * consumer
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/5/14
 * Time: 上午11:40
 */

namespace Kafka_php\test;

use Kafka\Consumer;
use Kafka_php\conf\Kafka;
use Kafka_php\conf\Keys;
use Kafka\ConsumerConfig;
use Kafka\Exception;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try {

    $config = ConsumerConfig::getInstance();
    $config->setMetadataRefreshIntervalMs(10000);
    $config->setMetadataBrokerList(Kafka::BROKER);
    $config->setGroupId(Keys::GROUP_DEMO);
    $config->setBrokerVersion(Kafka::VERSION);
    $config->setTopics(array(Keys::TOPIC_TEST_DEMO));

    $consumer = new Consumer();
    $consumer->start(function ($topic, $part, $message) {
        echo $topic ."\r\n";
        echo $part . "\r\n";
        var_dump($message);
    });

} catch (Exception $e) {
    echo $e->getMessage();
}