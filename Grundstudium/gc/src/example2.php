<?php
/**
 * 1. 在php5.6 运行
 *      int(221144)
        a: (refcount=1, is_ref=0)='aaa'
        int(221328)
        a: (refcount=1, is_ref=0)='aab'
        int(221360)
        a: no such symbol
        int(221192)
 * 2. 在php7.2 运行
 *      int(389504)
        a: (refcount=0, is_ref=0)='aaa'
        int(389536)
        a: (refcount=1, is_ref=0)='aab'
        int(389568)
        a: no such symbol
        int(389536)
 *  结论：php7+
 *          1. zval不再是单独堆分配，不再是自行存储refcount。
 *          2. 数字简单类型值不需要单独分配内存，也不使用引用计数。复杂类型（字符串,数组和对象）单独分配内存和自身存储引用计数。
 *          3. 初始化的字符串也是不使用引用计数，只有在计算中才会存储引用计数。
 *          4. 不会再有两次计数的情况。在对象中，只有对象自身存储的计数是有效的。
 *          5. 由于现在计数由数值自身存储（PHP 有 zval 变量容器存储），所以也就可以和非 zval 结构的数据共享，比如 zval 和 hashtable key 之间。
 *          6. 间接访问需要的指针数减少了。
 *      内存回收：
 *          1. php5 unset后立即回收；
 *          2. php7+ unset 没有立即回收，
 *          3. 内存回收的触发点是refcount-1： php7：在处理++运算之前a的refcount是0,如果这时候unset，也不会内存回收；
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/21
 * Time: 下午5:01
 */

var_dump(memory_get_usage());
$a = 'aaa';
xdebug_debug_zval('a');
var_dump(memory_get_usage());
$a++;
xdebug_debug_zval('a');
var_dump(memory_get_usage());
unset($a);
xdebug_debug_zval('a');
var_dump(memory_get_usage());