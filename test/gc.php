<?php
/**
 * php gc 垃圾回收器
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/21
 * Time: 下午4:07
 */
$a = 1;
$b = &$a;
xdebug_debug_zval('a');
xdebug_debug_zval('b');
$a =10;
xdebug_debug_zval('a');
xdebug_debug_zval('b');
unset($a);
xdebug_debug_zval('a');
xdebug_debug_zval('b');