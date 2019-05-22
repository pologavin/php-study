<?php
/**
 * 单例模式
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/15
 * Time: 下午5:10
 */
namespace Design;

trait singletonTrait
{
    private static $instances = [];

    private function __construct()
    {
        echo 66 . "\r\n";
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        $className = get_called_class();
        $args = func_get_args();

        $key = md5($className . ':' . serialize($args));
        if(!isset(self::$instances[$key])) {
            self::$instances[$key] = new $className(...$args);
        }

        return self::$instances[$key];
    }
}