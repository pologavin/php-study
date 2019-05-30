<?php
/**
 * 主消费入口
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/11/21
 * Time: 下午5:18
 */
namespace kafka;

use kafka\lib\Consumer;
use kafka\lib\SimpleConsumer;

class MainConsumer
{
    public static function start($broker, $callback, $topic, $groupId, array $partitions = [])
    {

        //指定ticks
        declare(ticks = 1);

        //安装信号处理器
        pcntl_signal(SIGHUP, array(MainConsumer::class, "sigHandler"));
        pcntl_signal(SIGINT, array(MainConsumer::class, "sigHandler"));
        pcntl_signal(SIGQUIT, array(MainConsumer::class, "sigHandler"));
        pcntl_signal(SIGTERM, array(MainConsumer::class, "sigHandler"));

        if(!empty($partitions)) {
            $consumer = new SimpleConsumer($broker);
            $consumer->setPartitions($partitions);
        }else{
            $consumer = new Consumer($broker);
        }
        $consumer->setMaxMessage(1);
        $consumer->setTopic($topic);
        $consumer->setGroupId($groupId);
        $consumer->setConsumeTimeout(100);
        $consumer->start($callback);
    }

    public static function sigHandler($sigNo)
    {
        switch ($sigNo) {
            case SIGHUP:
            case SIGQUIT:
            case SIGTERM:
            case SIGINT:
                Consumer::stop();
                break;
            default:
        }
    }
}