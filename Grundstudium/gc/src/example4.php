<?php
/**
 *  1. 在php5.6 运行
 *          int(221192)
            a: (refcount=1, is_ref=0)=class A { public $a = (refcount=2, is_ref=0)='aaa' }
            int(222616)
            a: no such symbol
            int(222144)
 *  2. 在php7.2 运行
 *          int(389248)
            a: (refcount=1, is_ref=0)=class A { public $a = (refcount=2, is_ref=0)='aaa' }
            int(389712)
            a: no such symbol
            int(389280)
 *  1. 结论：
 *         1. 对象类型初始化refcount=1，属性初始化refcount为2（一样）
 *         2. unset可以立即回收
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/22
 * Time: 下午3:38
 */
var_dump(memory_get_usage());
class A
{
    public $a = 'aaa';
}
$a = new A();
xdebug_debug_zval('a');
var_dump(memory_get_usage());
unset($a);
xdebug_debug_zval('a');
var_dump(memory_get_usage());