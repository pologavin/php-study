<?php
/**
 * redis 配置
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/9/10
 * Time: 上午10:39
 */

namespace redis\conf;

class Redis
{
    public static $connect = [
        'host' => 'redis',
        'port' => 6379,
        'timeout' => 1.0, //s
        'pconnect' => false,
    ];
}