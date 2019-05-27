<?php

/**
 * @link https://leetcode.com/problems/reverse-linked-list/
 * 翻转链表
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/27
 * Time: 上午11:18
 */
class ReverseLinkedList
{
    public function main(SplDoublyLinkedList $list)
    {
        $count = $list->count();
        $newList = new SplDoublyLinkedList();
        for($i = 0; $i < $count; $i++){
            $value = $list->pop();
            $newList->push($value);
        }
        return $newList;
    }

}


$list = new SplDoublyLinkedList();
for ($i = 1; $i <= 5; $i++) {
    $list->push($i);
}
var_dump($list);

$res = (new ReverseLinkedList())->main($list);

var_dump($res);