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