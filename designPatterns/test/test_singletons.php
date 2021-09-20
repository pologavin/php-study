<?php
/**
 * 多个单例使用问题
 *      1. 当两个单例在其中一个单例中获取另一个单例，当一个单例的属性发生变化也就会应该另一个单例的使用。
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/31
 * Time: 上午11:08
 */

namespace design\test;

use Design\singletonTrait;

define('ROOT', dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . 'singletonTrait.php';

class testSingletons
{
    use singletonTrait;

    /**
     * @var baseSingleton
     */
    protected $singleton;

    public function __construct()
    {
        $this->singleton = baseSingleton::getInstance();
    }

    public function main()
    {
        var_dump($this->singleton->getConn());
    }
}

class baseSingleton
{
    use singletonTrait;
    protected static $conn = [];

    public function __construct()
    {
        self::$conn = [124];
    }

    public function getConn()
    {
        return self::$conn;
    }

    public function destructConn()
    {
        // TODO: Implement __destruct() method.
        self::$conn = [];
    }
}

$test = testSingletons::getInstance();
$test->main();

baseSingleton::getInstance()->destructConn();

$test->main();

