<?php
/**
 * 数组递归
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/29
 * Time: 上午10:26
 */

$json = '[{"id":"1","pid":"0","name":"2","children":[{"id":"7","pid":"1","name":"22"},{"id":"8","pid":"1","name":"23","children":[{"id":"9","pid":"8","name":"24"}]}]},{"id":"2","pid":"0","name":"1"},{"id":"3","pid":"0","name":"1","children":[{"id":"10","pid":"3","name":"25"},{"id":"11","pid":"3","name":"26"}]},{"id":"4","pid":"0","name":"1"},{"id":"5","pid":"0","name":"1"},{"id":"6","pid":"0","name":"2"}]';
$arr = json_decode($json, true);

$keys = [7, 8, 9];
/*array_walk_recursive($arr, function (&$v, $k, $keys){
    echo $k . "\r\n";
    if($k == 'pid' && !in_array($v, $keys)) {
        echo 99;
        unset($v);
    }
}, $keys);*/

function func(&$arr, $keys)
{
    foreach ($arr as $k => &$item) {
        if (!empty($item['children'])) {
            func($item['children'], $keys);
        } elseif (!in_array($item['id'], $keys)) {
            array_splice($arr, $k, 1);
        }
    }
    return $arr;
}

var_dump($arr);

$res = array_column($arr, 'id');
var_dump($res);