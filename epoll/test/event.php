<?php
/**
 * event
 * User: gaojun
 * Date: 2019/5/16
 * Time: 下午2:42
 */
// 初始化一个EventConfig
$eventConfig = new EventConfig();
// 根据EventConfig初始化一个EventBase
$eventBase = new EventBase( $eventConfig );
// 初始化一个定时器event
$timer = new Event( $eventBase, -1, Event::TIMEOUT | Event::PERSIST, function(){
    echo microtime( true )." : 起飞！".PHP_EOL;
} );
// tick间隔为0.05秒钟，我们还可以改成0.5秒钟甚至0.001秒，也就是毫秒级定时器
$tick = 0.05;
// 将定时器event添加（可以不传 $tick）
$timer->add( $tick );
// eventBase进入loop状态
$eventBase->loop();

