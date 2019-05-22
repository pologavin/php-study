php 协程实现

### 什么原理
1. 迭代器是以迭代器模式实现的用于遍历的接口。
2. php5.5引入关键字yield，yield 是迭代生成器的实现。
3. 基于yield可以在程序上自行实现多任务调度的协作非阻塞方案，这就是php的协程；
4. php语言本身是没有完善的php协程方案；跟go的goroutine实现方式有很大区别；
5. 生成器是一种具有中断点的函数，而yield构成了中断点。
6. PHP的协程支持是在迭代生成器的基础上，增加了可以回送数据给生成器的功能，从而达到双向通信即：
```
生成器<---数据--->调用者
```

### 什么是协程
1. 协程本身的概念就是用户态的线程。其通过协作而不是抢占来进行切换。
2. 协程为协同任务提供了一种运行时抽象，这种抽象非常适合于协同多任务调度和数据流处理。在现代操作系统和编程语言中，因为用户态线程切换代价比内核态线程小，协程成为了一种轻量级的多任务模型。
3. 协程的控制由应用程序显式调度，非抢占式的。
4. 协程的执行最终靠的还是线程，应用程序来调度协程选择合适的线程来获取执行权。
5. 切换非常快，成本低。一般占用栈大小远小于线程（协程KB级别，线程MB级别），所以可以开更多的协程。
6. 特点就是执行中断，切换上下文。


### 怎么实现
1. 任务器
> 简单的定义具有任何ID标识的协程函数. 协程执行从生成器的current()开始。

```
<?php
/**
 * 任务
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/3
 * Time: 上午11:37
 */

class Task
{
    // 任务 ID
    protected $taskId;
    // 协程对象
    protected $coroutine;
    // send() 值
    protected $sendVal = null;
    // 是否首次 yield
    protected $beforeFirstYield = true;

    protected $exception = null;

    public function __construct($taskId, Generator $coroutine)
    {
        $this->taskId = $taskId;
        $this->coroutine = $coroutine;
    }

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function setSendValue($sendVal)
    {
        $this->sendVal = $sendVal;
    }

    public function setException($exception) {
        $this->exception = $exception;
    }

    public function run()
    {
        // 如之前提到的在send之前, 当迭代器被创建后第一次 yield 之前，一个 renwind() 方法会被隐式调用
        // 所以实际上发生的应该类似:
        // $this->coroutine->rewind();
        // $this->coroutine->send();

        // 这样 renwind 的执行将会导致第一个 yield 被执行, 并且忽略了他的返回值.
        // 真正当我们调用 yield 的时候, 我们得到的是第二个yield的值，导致第一个yield的值被忽略。
        // 所以这个加上一个是否第一次 yield 的判断来避免这个问题
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            return $this->coroutine->current();
        } elseif ($this->exception) {
            $retval = $this->coroutine->throw($this->exception);
            $this->exception = null;
            return $retval;
        } else {
            $retval = $this->coroutine->send($this->sendVal);
            $this->sendVal = null;
            return $retval;
        }
    }

    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}
```

2. 协程调度器
> 使用SplQueue队列执行，等待，退出实现多个任务相互调度。


```
<?php
/**
 * 调度器
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2018/12/3
 * Time: 上午11:40
 */

class Scheduler
{
    protected $maxTaskId = 0;
    protected $tasks = []; // taskId => task
    protected $queue;

    // resourceID => [socket, tasks]
    protected $waitingForRead = [];
    protected $waitingForWrite = [];

    public function __construct()
    {
        // SPL 队列
        $this->queue = new SplQueue();
    }

    public function newTask(Generator $coroutine)
    {
        $tid = ++$this->maxTaskId;
        $task = new Task($tid, $coroutine);
        $this->tasks[$tid] = $task;
        $this->schedule($task);
        return $tid;
    }

    public function killTask($tid) {
        if (!isset($this->tasks[$tid])) {
            return false;
        }

        unset($this->tasks[$tid]);

        // This is a bit ugly and could be optimized so it does not have to walk the queue,
        // but assuming that killing tasks is rather rare I won't bother with it now
        foreach ($this->queue as $i => $task) {
            if ($task->getTaskId() === $tid) {
                unset($this->queue[$i]);
                break;
            }
        }

        return true;
    }

    public function schedule(Task $task)
    {
        // 任务入队
        $this->queue->enqueue($task);
    }

    public function run()
    {
        //$this->newTask($this->ioPollTask());//产生“常驻任务”，负责检查socket事件
        while (!$this->queue->isEmpty()) {
            $task = $this->queue->dequeue();
            $retval = $task->run();
            if ($retval instanceof SystemCall) {
                $retval($task, $this);
                continue;
            }

            if ($task->isFinished()) {
                unset($this->tasks[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
}
```
