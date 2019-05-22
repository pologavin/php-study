<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/3
 * Time: 下午1:59
 */
require_once 'task.php';
require_once 'scheduler.php';
require_once 'SystemCall.php';

define("ROOT", dirname(__FILE__));

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

function task3()
{
    $tid = (yield getTaskId());
    $childTid = (yield newTask(childTask()));

    for ($i = 1; $i <= 6; ++$i) {
        echo "Parent task 3 $tid iteration $i.\n";
        yield;

        if ($i == 3) yield killTask($childTid);
    }

}
$i = 100000;
function task4(){
    global $i;
    echo "wait start" . PHP_EOL;
    while ($i-- > 0) {
        yield;
    }
    echo "wait end" . PHP_EOL;
};

function task5(){
    echo "Hello " . PHP_EOL;
    file_put_contents(ROOT . DIRECTORY_SEPARATOR . 'data.log', 'hello'. PHP_EOL, FILE_APPEND);
    yield atest();
    echo "world!" . PHP_EOL;
    file_put_contents(ROOT . DIRECTORY_SEPARATOR . 'data.log', 'world!'. PHP_EOL, FILE_APPEND);
}
function task6()
{
    echo "over!" . PHP_EOL;
    return;
}

function atest()
{
    sleep(2);
    echo 888 . PHP_EOL;
    return true;
}

$scheduler = new Scheduler;

//$scheduler->newTask(task1());
//$scheduler->newTask(task2());
//$scheduler->newTask(task3());
//$scheduler->newTask(task4());
$scheduler->newTask(task5());
task6();
$scheduler->run();
