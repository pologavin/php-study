<?php
/**
 *  1. 在php5.6 运行
 *      int(221192)
        a: (refcount=1, is_ref=0)=array ()
        int(221416)
        a: no such symbol
        int(221192)
 *  2. 在php7.2 运行
 *      int(388408)
        a: (refcount=2, is_ref=0)=array ()
        int(388440)
        a: no such symbol
        int(388440)
 *  3. 结论：
 *      1. php7+ 数组类型refcount 初始化为2； php5 初始化为1
 *      2. 内存回收：
 *              1. php5 unset后自动回收（此时refcount-1==0）；
 *              2. php7 unset 没有自动回收（此时refcount-1==1）
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/5/21
 * Time: 下午5:18
 */

var_dump(memory_get_usage());
$a = ['a' => 1, 'b' => 2, 'c' => 3];
xdebug_debug_zval('a');
var_dump(memory_get_usage());
unset($a);
xdebug_debug_zval('a');
var_dump(memory_get_usage());