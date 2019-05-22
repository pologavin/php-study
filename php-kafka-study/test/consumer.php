<?php
/**
 * 消费测试
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/11/21
 * Time: 下午5:40
 */

namespace kafka\test;

use kafka\conf\Kafka;
use kafka\conf\Keys;
use kafka\MainConsumer;

define('ROOT', dirname(__DIR__));
register_shutdown_function("error_handler");

require_once ROOT . DIRECTORY_SEPARATOR . 'autoload.php';

class Consumer
{
    public function main()
    {
        $config = Kafka::BROKER;
        MainConsumer::start($config, [$this, 'consume'], Keys::TOPIC_TEST_DEMO, Keys::GROUP_DEMO, [0]);
    }

    public function consume($messages)
    {
        // 消息处理逻辑
        var_dump($messages);
    }

}

try{
    (new Consumer())->main();
}catch (\Exception $e) {
    echo $e->getMessage();
}

function error_handler()
{
    if(!empty(error_get_last())) {
        file_put_contents(ROOT . DIRECTORY_SEPARATOR . 'logs/err.log', var_export(error_get_last(), true), FILE_APPEND);
    }
}

