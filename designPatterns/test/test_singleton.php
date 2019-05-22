<?php
/**
 * 单例测试
 * 测试结果：
 *  1. 单例模式在单个进程中只存在一个实例；
 *  2. 在使用反射类不用构造器new一个对象会破坏单例模式；
 *  3. 多进程编程下是会破坏单例模式的；主进程和子进程是共享一个实例；但子进程之间不会共享一个实例；
 *  4. 而fpm是多进程模型，所以单例模式只是针对单个请求。并发请求下是这种单例模式是被破坏的。
 *
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/16
 * Time: 下午1:23
 */

namespace design\test;

use Design\singletonTrait;
use design\synchronizationSingleton;

define('ROOT', dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . 'singletonTrait.php';

require_once ROOT . DIRECTORY_SEPARATOR . 'synchronizationSingleton.php';

class TestSingleton
{
    use singletonTrait;

    public function main()
    {
        echo 'this is singleton sample!' . "\r\n";
    }

}

class TestSynchronization extends synchronizationSingleton
{
    public function main()
    {
        echo 'this is synchronization singleton sample!' . "\r\n";
    }
}

/*$obj0 = TestSingleton::getInstance();
$obj = TestSingleton::getInstance();
if ($obj === $obj0) {
    echo 5;
}
//$obj->main();

// 反射对象测试
$reflection = new \ReflectionClass(TestSingleton::class);
$obj1 = $reflection->newInstanceWithoutConstructor();
$obj2 = $reflection->newInstanceWithoutConstructor();
if ($obj1 === $obj2) {
    echo 1;
}*/

// 多进程测试
$obj = TestSingleton::getInstance();
for ($i = 0; $i < 10; $i++) {
    $pid = pcntl_fork();
    global $obj1;
    global $obj2;
    switch ($pid) {
        case -1:
            print "创建子进程失败!" . PHP_EOL . "\r\n";
            exit;
        case 0:
            $obj1 = TestSingleton::getInstance();
            $obj2 = TestSynchronization::getInstance();
            break;
        default:
            //$obj2 = TestSingleton::getInstance();
            pcntl_wait($status); // 子进程执行完后才执行父进程
            break;
    }
}



