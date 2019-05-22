<?php
/**
 * 单例模型
 * User: gaojun
 * Date: 2018/10/30
 * Time: 下午4:16
 */
namespace elasticSearch;

trait InstanceTrait
{
    private static $instances;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        $className = get_called_class();
        $args = func_get_args();
        //若$args中有resource类型的参数,则无法区分同一个类的不同实例
        $key = md5($className . ':' . serialize($args));
        if (!isset(self::$instances[$key])) {
            //PHP_VERSION >= 5.6.0
            self::$instances[$key] = new $className(...$args);
        }
        return self::$instances[$key];
    }
}