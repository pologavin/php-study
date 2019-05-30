<?php
/**
 * @link https://www.php.net/manual/zh/class.splfixedarray.php
 * 定长数组
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 上午11:47
 */

$arr = new SplFixedArray(2);

$arr[0] =1;
$arr[1] =2;
$arr->setSize(3);
$arr[2] =3;

var_dump($arr);
