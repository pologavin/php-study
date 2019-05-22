<?php
/**
 *  1. 在 php5.6 运行
 *          int(221192)
            a: (refcount=1, is_ref=0)='aaa'
            int(221360)
            a: no such symbol
            int(221192)
 *  2. 在 php7.2 运行
 *          int(388656)
            a: (refcount=1, is_ref=0)='aaa'
            int(388728)
            a: no such symbol
            int(388688)
 *  3. 结论：
 *       1. php7在运算中refcount会初始化1；
 *       2. 内存回收
 *              1. unset均可立即回收
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/5/21
 * Time: 下午4:43
 */

var_dump(memory_get_usage());
$a = str_repeat('a', 3);
xdebug_debug_zval('a');
var_dump(memory_get_usage());
unset($a);
xdebug_debug_zval('a');
var_dump(memory_get_usage());