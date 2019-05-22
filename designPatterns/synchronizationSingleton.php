<?php
/**
 * synchronization 线程同步锁
 * Created by PhpStorm.
 * User: gavin
 * Date: 2018/12/16
 * Time: 下午10:35
 */

namespace design;

class synchronizationSingleton extends \Threaded
{
    private static $instances = [];

    private function __construct()
    {
        echo 'this new construct instance' . '\r\n';
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

        (new synchronizationSingleton)->synchronized(function ($key, $className, $args){
            if(!isset(self::$instances[$key])) {
                self::$instances[$key] = new $className(...$args);
            }
        });

        return self::$instances[$key];
    }
}