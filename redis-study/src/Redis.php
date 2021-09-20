<?php
/**
 * redis 操作类
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/9/10
 * Time: 下午12:00
 */

namespace redis;

/**
 * https://github.com/phpredis/phpredis/tree/2.2.8
 * php-redis version:2.2.8
 *
 * @method string get($key)
 * @method bool set($key, $value, $timeout = 0)
 * @method bool setex($key, $ttl, $value)
 * @method bool psetex($key, $ttl, $value)
 * @method bool setnx($key, $value)
 * @method int del(array | string $key1, $key2 = null, $keyN = null)
 * @method bool exists($key)
 * @method array mget(array $keys)
 * @method bool mset(array $items)
 * @method bool expire($key, $ttl)
 * @method bool expireAt($key, $timestamp)
 * @method int hSet($key, $hashKey, $value)
 * @method string hGet($key, $hashKey)
 * @method int hLen($key)
 * @method int hDel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null)
 * @method array hKeys($key)
 * @method array hVals($key)
 * @method array hGetAll($key)
 * @method bool hExists($key, $hashKey)
 * @method bool hMset($key, $hashItems)
 * @method array hMGet($key, $hashKeys)
 * @method string lIndex($key, $index)
 * @method string lPop($key)
 * @method int lPush($key, $value1, $value2 = null, $valueN = null)
 * @method array lRange($key, $start, $end)
 * @method array lTrim($key, $start, $stop)
 * @method int lRem($key, $value, $count)
 * @method string rPop($key)
 * @method int rPush($key, $value1, $value2 = null, $valueN = null)
 * @method int lLen($key)
 * @method array blPop(array | string $keys, $timeout)
 * @method array brPop(array | string $keys, $timeout)
 * @method string rpoplpush($srcKey, $dstKey)
 * @method string brpoplpush($srcKey, $dstKey, $timeout)
 * @method int sAdd($key, $value1, $value2 = null, $valueN = null)
 * @method int sCard($key)
 * @method bool sIsMember($key, $value)
 * @method array sMembers($key)
 * @method int sRem($key, $member1, $member2 = null, $memberN = null)
 * @method int zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null)
 * @method int zCard($key)
 * @method float zIncrBy($key, $value, $member)
 * @method array zRange($key, $start, $end, $withScores = false)
 * @method array zRevRange($key, $start, $end, $withScores = false)
 * @method array zRangeByScore($key, $start, $end, $options = [])
 * @method array zRevRangeByScore($key, $start, $end, $options = [])
 * @method int zRem($key, $member1, $member2 = null, $memberN = null)
 * @method int zRemRangeByScore($key, $start, $end)
 * @method float zScore($key, $member)
 * @method Redis multi($type = \Redis::MULTI)
 * @method mixed exec()
 */

class Redis
{
    use InstanceTrait;

    /**
     * @var array 主库配置
     */
    private $masterConfig = [];
    /**
     * @var \Redis 主redis
     */
    private $masterRedis;
    /**
     * @var array 从库配置
     */
    private $slaveConfig = [];
    /**
     * @var \Redis 从redis
     */
    private $slaveRedis;

    /**
     * @var array 读写分离之读操作列表：方法名统一小写
     */
    private $methodsByReadOp = ['get', 'exists', 'mget', 'hget', 'hlen', 'hkeys', 'hvals', 'hgetall', 'hexists',
        'hmget', 'lindex', 'lget', 'llen', 'lsize', 'lrange', 'lgetrange', 'scard', 'ssize', 'sdiff', 'sinter',
        'sismember', 'scontains', 'smembers', 'sgetmembers', 'srandmember', 'sunion', 'zcard', 'zsize',
        'zcount', 'zrange', 'zrangebyscore', 'zrevrangebyscore', 'zrangebylex', 'zrank', 'zrevrank', 'zrevrange',
        'zscore', 'zunion'];

    const MULTI = \Redis::MULTI;
    const PIPELINE = \Redis::PIPELINE;

    const RW_TYPE_MASTER = 'm';
    const RW_TYPE_SLAVE = 's';

    /**
     * @var bool 是否在事务中
     */
    private $inTrans = false;
    /**
     * @var int 错误码
     */
    private $errorCode = 0;

    /**
     * @var null 前置回调函数
     */
    private static $beforeExecuteCallback = null;
    /**
     * @var null 后置回调函数
     */
    private static $afterExecuteCallback = null;


    private function __construct(array $config)
    {
        if (isset($config['master'])) {
            //支持读写分离的主从配置
            $this->masterConfig = $config['master'];
            if (!empty($config['slaves']) && is_array($config['slaves'])) {
                $randKey = array_rand($config['slaves']);
                $this->slaveConfig = $config['slaves'][$randKey];
            }
        } else {
            //单例配置
            $this->masterConfig = $config;
        }
    }

    private function callBeforeExecuteCallback()
    {
        if (self::$beforeExecuteCallback && is_callable(self::$beforeExecuteCallback)) {
            call_user_func_array(self::$beforeExecuteCallback, [$this]);
        }
    }

    private function callAfterExecuteCallback()
    {
        if (self::$afterExecuteCallback && is_callable(self::$afterExecuteCallback)) {
            call_user_func_array(self::$afterExecuteCallback, [$this]);
        }
    }

    private function getRwType($rwType)
    {
        if ($rwType != self::RW_TYPE_SLAVE || $this->inTrans || !$this->slaveConfig) {
            return self::RW_TYPE_MASTER;
        } else {
            return self::RW_TYPE_SLAVE;
        }
    }

    private function errorLog($message, $method, $params, $rwType = null)
    {
        if ($this->getRwType($rwType) == self::RW_TYPE_MASTER) {
            $this->masterRedis = null;
        } else {
            $this->slaveRedis = null;
        }

        $errStr = date("Y-m-d H:i:s") . ' | ' . $method . ' | ' . $message . ' | ' . json_encode($params) . PHP_EOL;
        file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs/error.log', $errStr, FILE_APPEND);
    }

    private function getRedisConnect(array &$redisConfig)
    {
        $host = isset($redisConfig['host']) ? $redisConfig['host'] : '';
        $port = isset($redisConfig['port']) ? (int)$redisConfig['port'] : 0;
        $timeout = isset($redisConfig['timeout']) ? (float)$redisConfig['timeout'] : 0;
        $pConnect = isset($redisConfig['pconnect']) ? (bool)$redisConfig['pconnect'] : false;
        $password = isset($redisConfig['password']) ? (string)$redisConfig['password'] : '';
        $redis = new \Redis();
        if ($pConnect) {
            $connectResult = $redis->pconnect($host, $port, $timeout);
        } else {
            $connectResult = $redis->connect($host, $port, $timeout);
        }
        if ($connectResult && $password) {
            $redis->auth($password);
        }
        return $redis;
    }

    private function getMasterConnect()
    {
        if (!$this->masterRedis) {
            $this->masterRedis = $this->getRedisConnect($this->masterConfig);
        }
        return $this->masterRedis;
    }

    private function getSlaveConnect()
    {
        if (!$this->slaveRedis) {
            $this->slaveRedis = $this->getRedisConnect($this->slaveConfig);
        }
        return $this->slaveRedis;
    }

    private function connect($rwType = null)
    {
        if ($this->getRwType($rwType) == self::RW_TYPE_MASTER) {
            return $this->getMasterConnect();
        } else {
            return $this->getSlaveConnect();
        }
    }

    public function __call($method, $params)
    {
        $this->callBeforeExecuteCallback();
        $methodToLower = strtolower($method);
        $rwType = null;
        if ($this->slaveConfig) {
            //有从库配置的时候才进行读写分离
            $rwType = in_array($methodToLower, $this->methodsByReadOp) ? self::RW_TYPE_SLAVE : self::RW_TYPE_MASTER;
            if ($methodToLower == 'multi') {
                $this->inTrans = true;
            } elseif ($methodToLower == 'exec') {
                $this->inTrans = false;
            }
        }
        $redis = $this->connect($rwType);
        $result = false;
        $this->errorCode = 0;
        try {
            $result = call_user_func_array([$redis, $method], $params);
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorLog($e->getMessage(), $method, $params, $rwType);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public static function setBeforeExecuteCallback(callable $callback)
    {
        self::$beforeExecuteCallback = $callback;
    }

    public static function setAfterExecuteCallback(callable $callback)
    {
        self::$afterExecuteCallback = $callback;
    }

    /**
     * 重写加数
     * @param $key
     * @param int $step
     * @return bool|float|int
     */
    public function incr($key, $step = 1)
    {
        $this->callBeforeExecuteCallback();
        $result = false;
        $rwType = null;
        $this->errorCode = 0;
        try {
            if ($this->slaveConfig) {
                //有从库配置的时候才进行读写分离
                $rwType = self::RW_TYPE_MASTER;
            }
            $connect = $this->connect($rwType);
            if ($step == 1) {
                $result = $connect->incr($key);
            } elseif (is_float($step)) {
                $result = $connect->incrByFloat($key, $step);
            } else {
                $result = $connect->incrBy($key, $step);
            }
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorLog($e->getMessage(), __FUNCTION__, func_get_args(), $rwType);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

    /**
     * 重写减数
     * @param $key
     * @param int $step
     * @return bool|float|int
     */
    public function decr($key, $step = 1)
    {
        $this->callBeforeExecuteCallback();
        $result = false;
        $rwType = null;
        $this->errorCode = 0;
        try {
            if ($this->slaveConfig) {
                //有从库配置的时候才进行读写分离
                $rwType = self::RW_TYPE_MASTER;
            }
            $connect = $this->connect($rwType);
            if ($step == 1) {
                $result = $connect->decr($key);
            } elseif (is_float($step)) {
                $result = $connect->incrByFloat($key, $step * -1);
            } else {
                $result = $connect->decrBy($key, $step);
            }
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorLog($e->getMessage(), __FUNCTION__, func_get_args(), $rwType);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

    /**
     * 重写hIncr
     * @param $key
     * @param $hashKey
     * @param int $step
     * @return bool|float|int
     */
    public function hIncr($key, $hashKey, $step = 1)
    {
        $this->callBeforeExecuteCallback();
        $result = false;
        $rwType = null;
        $this->errorCode = 0;
        try {
            if ($this->slaveConfig) {
                //有从库配置的时候才进行读写分离
                $rwType = self::RW_TYPE_MASTER;
            }
            $connect = $this->connect($rwType);
            if (is_float($step)) {
                $result = $connect->hIncrByFloat($key, $hashKey, $step);
            } else {
                $result = $connect->hIncrBy($key, $hashKey, $step);
            }
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorLog($e->getMessage(), __FUNCTION__, func_get_args(), $rwType);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

    /**
     * 重写 hDecr
     * @param $key
     * @param $hashKey
     * @param int $step
     * @return bool|float|int
     */
    public function hDecr($key, $hashKey, $step = 1)
    {
        $this->callBeforeExecuteCallback();
        $result = false;
        $rwType = null;
        $this->errorCode = 0;
        try {
            if ($this->slaveConfig) {
                //有从库配置的时候才进行读写分离
                $rwType = self::RW_TYPE_MASTER;
            }
            $connect = $this->connect($rwType);
            if (is_float($step)) {
                $result = $connect->hIncrByFloat($key, $hashKey, $step * -1);
            } else {
                $result = $connect->hIncrBy($key, $hashKey, $step * -1);
            }
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorLog($e->getMessage(), __FUNCTION__, func_get_args(), $rwType);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

}