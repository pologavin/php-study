<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/3
 * Time: 下午1:59
 */
require_once 'task.php';
require_once 'scheduler.php';

function task1()
{
    for ($i = 1; $i <= 10; ++$i) {
        echo "This is task 1 iteration $i.\n";
        yield;
    }
}

function task2()
{
    for ($i = 1; $i <= 5; ++$i) {
        echo "This is task 2 iteration $i.\n";
        yield;
    }
}

$scheduler = new Scheduler;

$scheduler->newTask(task1());
$scheduler->newTask(task2());

$scheduler->run();