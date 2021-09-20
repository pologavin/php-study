<?php
/**
 * php array
 *  1. 执行参考结果：
        for 执行时间：0.16188621520996毫秒
        foreach 执行时间：0.32997131347656毫秒
        array_map 执行时间：0.86092948913574毫秒
        array_map + func 执行时间：0.43606758117676毫秒
        array_walk + func 执行时间：0.64492225646973毫秒
 *  2. 结论：
 *      1. for的性能比foreach高一倍多，原因foreach内部是使用数组的链表结构；
 *      2. foreach 不带key=> 比带性能高近一倍；
 *      3. array_map/array_walk 函数比foreach的性能低很多；
 *      4. foreach 带&的性能低于不带，且低于for；
 *      5. array_map等函数使用闭包回调比使用系统函数的性能要低；
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/6/9
 * Time: 下午4:16
 */
$count = 10000;
$data = range(0, $count);

/*$data = array_fill(0, $count, array(
    'id' => 1,
    'name' => 'test',
    'remark' => 'ok'
));*/

function add($value) {
    $value ++;
    return $value;
}

$startTime = microtime(true);
for ($i = 0; $i < $count; $i++) {
    $data[$i]++;
}
echo 'for 执行时间：' . (microtime(true) - $startTime) * 1000 . '毫秒' . PHP_EOL;

$startTime = microtime(true);
foreach ($data as &$value) {
    $value++;
}
echo 'foreach 执行时间：' . (microtime(true) - $startTime) * 1000 . '毫秒' . PHP_EOL;

$startTime = microtime(true);
array_map('trim', $data);
echo 'array_map 执行时间：'. (microtime(true) - $startTime) * 1000 . '毫秒'.PHP_EOL;

$startTime = microtime(true);
array_map('add', $data);
echo 'array_map + func 执行时间：'. (microtime(true) - $startTime) * 1000 . '毫秒'.PHP_EOL;

$startTime = microtime(true);
array_walk($data, 'add');
echo 'array_walk + func 执行时间：'. (microtime(true) - $startTime) * 1000 . '毫秒'.PHP_EOL;
