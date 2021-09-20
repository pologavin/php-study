<?php
/**
 * redis queue
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/9/16
 * Time: 下午3:51
 */

namespace redis\queue;

use redis\Redis;

class Queue
{
    /**
     * @var int 阻塞超时时间
     */
    const BLOCK_TIME = -1;

    /**
     * @var Redis
     */
    protected $driver;
    /**
     * @var string 队列key
     */
    protected $queueKey = '';

    public function __construct($key)
    {
        $config = \redis\conf\Redis::$connect;
        $this->driver = Redis::getInstance($config);
        $this->queueKey = $key;
    }

    /**
     * 入队列
     * @param $message1
     * @param mixed ...$messageN
     * @return int
     */
    public function push($message1, ...$messageN)
    {
        return $this->driver->rPush($this->queueKey, $message1, ...$messageN);
    }

    /**
     * 出队列
     * @return string
     */
    public function pop()
    {
        return $this->driver->lPop($this->queueKey);
    }

    /**
     * 阻塞方式出队列
     * @param int $blockTime
     * @return array
     */
    public function bPop($blockTime = self::BLOCK_TIME)
    {
        return $this->driver->blPop($this->queueKey, $blockTime);
    }

    /**
     * 队列计数
     * @return int
     */
    public function count()
    {
        return $this->driver->hLen($this->queueKey);
    }
}