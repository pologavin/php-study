<?php
/**
 * Created by PhpStorm.
 * User: gaojun
 * Date: 2019/5/17
 * Time: 下午5:50
 */

namespace async_study\test;

use async_study\http\AsyncHttp;
use async_study\http\lib\Task;

date_default_timezone_set("PRC");
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {
    $async = new AsyncHttp();
    $startTime = microtime(true);
    $task = Task::createGet("http://localhost/test/redis_list.php");
    $async->attach($task, "redis");
    $task2 = Task::createGet("http://localhost/test/test_pdo.php");
    $async->attach($task2, "mysql" );
    $task3 = Task::createGet("http://www.qq.com");
    $async->attach($task3, "qq")->then(function ($data) {
        echo 'success:' . var_export($data, true) . PHP_EOL;
    },
        function ($data) {
            echo 'error:' . var_export($data, true) . PHP_EOL;
        }
    );
    $result = $async->execute();

    $endTime = microtime(true);
    echo '执行时间：' . ($endTime - $startTime) * 1000 . "毫秒\r\n";
    var_dump($result);
    die();
} catch (\Exception $e) {
    echo $e->getMessage();
}
