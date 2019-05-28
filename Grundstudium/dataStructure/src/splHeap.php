<?php
/**
 * @link https://www.php.net/manual/zh/class.splheap.php
 * 二叉堆
 * 抽象方法compare决定堆栈的排序：
 *          1. 大头堆：return $value1 < $value2 ? -1 : 1;
 *          2. 小头堆：$value1 > $value2 ? -1 : 1;
 *          3. 如果是数组，需要定制使用key还是value排序；
 *          4. 更为复杂的排序，可以定制重写compare方法
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 上午9:53
 */

class Heap extends SplHeap
{
    public function compare($value1, $value2)
    {
        // TODO: Implement compare() method.
        if ($value1 === $value2) return 0;
        // 大头堆
        //return $value1 < $value2 ? -1 : 1;
        // 小头堆
        return $value1 > $value2 ? -1 : 1;
    }
}

$heap = new Heap();

$heap->insert('c');
$heap->insert('a');
$heap->insert('b');

/*foreach ($heap as $value) {
    var_dump($value);
}*/

var_dump($heap->isEmpty());

for ($heap->top(); $heap->valid(); $heap->next()) {
    var_dump($heap->current());
}

// 小头堆
$minHeap = new SplMinHeap();

$minHeap->insert(123);
$minHeap->insert(456);
$minHeap->insert(4);

/*foreach ($minHeap as $value) {
    var_dump($value);
}*/

for ($minHeap->top(); $minHeap->valid(); $minHeap->next()) {
    var_dump($minHeap->current());
}

var_dump($minHeap);


// 大头堆
$maxHeap = new SplMaxHeap();

$maxHeap->insert(123);
$maxHeap->insert(456);
$maxHeap->insert(4);

for ($maxHeap->top(); $maxHeap->valid(); $maxHeap->next()) {
    var_dump($maxHeap->current());
}

var_dump($maxHeap);


// 优先队列
$priorityQueue = new SplPriorityQueue();

$priorityQueue->insert(123,6);
$priorityQueue->insert(456,4);
$priorityQueue->insert(4, 5);

// 提取flag
$flag = $priorityQueue->getExtractFlags();
var_dump($flag);

$priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);


for ($priorityQueue->top(); $priorityQueue->valid(); $priorityQueue->next()) {
    var_dump($priorityQueue->current());
}
