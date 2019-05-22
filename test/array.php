<?php
/**
 *
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/28
 * Time: 下午2:36
 */

$a = [
    [
        'a' => 1,
        'b' => 2
    ],
    [
        'a' => 2,
        'b' => 3
    ],
];

$c = array(
    'a' => [
      'a' => 1,
      'b' => 2
    ],
    'b' => [
        'a' => 1,
        'b' => 2
    ],
);

$b = [
    [
        'a' => 1,
        'c' => 2
    ],
    [
        'a' => 2,
        'c' => 3
    ],
];

array_walk($a, function (&$v, $k, $b) {
    if($v['a'] == $b[$k]['a']) {
        $v['c'] = $b[$k]['c'];
    }
}, $b);

var_dump($a);
