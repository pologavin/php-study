<?php
/**
 * 异步mysql
 * Created by PhpStorm.
 * User: gaojun
 * Date: 2019/5/19
 * Time: 下午5:07
 */
namespace async_study\test;

use async_study\mysql\AsyncMysql;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try{
    $startTime = microtime(true);
    $async = new AsyncMysql();
    $async->attach(
        ['host' => 'mysql', 'user' => 'root', 'password' => '123456', 'database' => 'test', 'port' => 3306],
        'select * from usertb where `uname` like "%1" limit 1000'
    );
    $async->attach(
        ['host' => 'mysql', 'user' => 'root', 'password' => '123456', 'database' => 'test', 'port' => 3306],
        'select * from usertb where `uname` like "%2" limit 1000'
    );

    $result = $async->execute();
    $endTime = microtime(true);
    echo '执行时间：' . ($endTime - $startTime) * 1000 . "毫秒\r\n";
    var_dump($result);

}catch (\Exception $e) {
    echo $e->getMessage();
}