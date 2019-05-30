<?php
/**
 * @link https://www.php.net/manual/zh/class.splstack.php
 * 堆栈
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/27
 * Time: 下午5:28
 */

$stack = new SplStack();

$stack->push(1);
$stack->push('a');

var_dump($stack);

foreach ($stack as $value) {
    var_dump($value);
}