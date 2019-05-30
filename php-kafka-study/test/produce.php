<?php
/**
 * 生产测试
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/11/21
 * Time: 下午5:39
 */

namespace kafka\test;

use kafka\conf\Kafka;
use kafka\conf\Keys;
use kafka\MainProducer;

define('ROOT', dirname(__DIR__));

register_shutdown_function("error_handler");

require_once ROOT . DIRECTORY_SEPARATOR . 'autoload.php';

class Produce
{
    public function main($count = 1000)
    {
        for ($i = 0; $i < $count; $i++) {
            $res = (new MainProducer(Kafka::BROKER))->produce(Keys::TOPIC_TEST_DEMO, json_encode(['id' => $i]), Keys::GROUP_DEMO, 1);
        }
        echo 'kafka produce ' . $count . ' success!';
    }
}

(new Produce())->main(10000);

function error_handler()
{
    if(!empty(error_get_last())) {
        file_put_contents(ROOT . DIRECTORY_SEPARATOR . 'logs/err.log', var_export(error_get_last(), true), FILE_APPEND);
    }
}
