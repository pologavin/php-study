<?php
/**
 * @link https://www.php.net/manual/zh/class.spldoublylinkedlist.php
 * 双链表
 * 默认模式是队列结构（先入先出）且迭代不删除
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/27
 * Time: 上午10:29
 */

$doubleList = new SplDoublyLinkedList();
$doubleList->push('a');
$doubleList->push('b');

var_dump($doubleList);

// 插入
$doubleList->add(1, 'bc');
var_dump($doubleList);

// 尾部弹出元素
$res = $doubleList->pop();
var_dump($res);

var_dump($doubleList);

// 末尾元素
$top = $doubleList->top();
var_dump($top);

// 开头元素
$bottom = $doubleList->bottom();
var_dump($bottom);

/*
  # 关于模式
  IT_MODE_LIFO: 2 Stack style, 后入先出，堆结构
  IT_MODE_FIFO: 0  Queue style, 先入先出，队列结构(默认)
  IT_MODE_DELETE: 1 Elements are deleted by the iterator 一边迭代，一边删除
  IT_MODE_KEEP: 0 Elements are traversed by the iterator 普通迭代，不删除(默认)
 */
$mode = $doubleList->getIteratorMode();
var_dump($mode);

foreach ($doubleList as $value) {
    var_dump($value);
}

var_dump($doubleList);

for ($doubleList->rewind(); $doubleList->valid(); $doubleList->next()) {
    var_dump($doubleList->current());
}

var_dump($doubleList);

// 设置迭代删除
$doubleList->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);

foreach ($doubleList as $value) {
    var_dump($value);
}

// 迭代为空
var_dump($doubleList);

// 堆栈模式
$doubleList->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);

$doubleList->push('c');
$doubleList->push('d');
var_dump($doubleList);

$res = $doubleList->pop();
var_dump($res);

var_dump($doubleList);

$doubleList->add(1, 'e');

var_dump($doubleList);

$top = $doubleList->top();
var_dump($top);

$bottom = $doubleList->bottom();
var_dump($bottom);

foreach ($doubleList as $value) {
    var_dump($value);
}