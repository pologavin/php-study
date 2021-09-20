<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/8/24
 * Time: 下午11:01
 */

$a = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

foreach ($a as &$v) {
    $v++;
}



var_dump($a);