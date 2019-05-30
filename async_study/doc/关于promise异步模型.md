### promise/A+ 规范
> 在promise之前，异步编程需要通过回调方式进行，回调方式一般会带来嵌套太多的问题，对代码维护工作带来了麻烦。而promise是异步编程的一种解决方案，比传统的解决方案——回调函数和事件——更合理和更强大。它由社区最早提出和实现，ES6将其写进了语言标准，统一了用法，原生提供了Promise对象。
1. Promise规范有很多，如Promise/A，Promise/B，Promise/D 以及 Promise/A 的升级版Promise/A+，因为ES6主要用的是Promise/A+规范，该规范内容也比较多，我们挑几个简单的说明下:
    1. Promise本身是一个状态机，每一个Promise实例只能有三个状态，pending（等待态）、fulfilled（执行态）、reject（拒绝态），状态之间的转化只能是pending->fulfilled、pending->reject，状态变化不可逆。
    2. Promise有一个then方法，该方法可以被调用多次，并且返回一个Promise对象（返回新的Promise还是老的Promise对象，规范没有提）。
    3. 支持链式调用；
    4. 内部保存有一个value值，用来保存上次执行的结果值，如果报错，则保存的是异常信息。
    

### promise/deferred模式
> 根据promise/A 或者它的增强修改版promise/A+ 规范 实现的promise异步操作的一种实现方式
1. 包含两部分：
    1. Deferred：主要是用于内部，来维护异步模型的状态。
    2. Promise：只要用于外部，通过then()方法，暴露给外部调用，以添加业务逻辑和业务的组装。    
2. 两部分关系：
![image](https://image-static.segmentfault.com/297/115/2971150790-5966ed72a8dc5)
     1. deferred对象通过resolve方法，改变自身状态为执行态，并触发then()方法的onfulfilled回调函数
     2. deferred对象通过reject方法，改变自身状态为拒绝态，并触发then()方法的onrejected回调函数
     
 3. 支持链式调用
 
 4. 统一的异步处理（拒绝处理）
 
 5. API promise化
 > 在编码的时候，想要用promise进行异步操作流程控制，就要将当前的异步回调函数封装成promise。在自己开发的时候，往往会去引用第三方的模块，然后发现这些模块的异步回调API 不支持promise写法。难道我们自己全部封装实现一遍？！这明显是不合理的。那我们就可以实现一个 方法可以批量将方法Promise化



### php的promise模型
1. 开源项目
    1. reactphp/promise：   https://github.com/reactphp/promise
    2. hprose-php：https://github.com/hprose/hprose-php/wiki/03-Promise-%E5%BC%82%E6%AD%A5%E7%BC%96%E7%A8%8B
    
2. 以reactphp的实现为例
    1.  Deferred对象：
```
// 1. Deferred对象实现resolve和reject改变promise对象状态
 public function promise()
    {
        if (null === $this->promise) {
            $this->promise = new Promise(function ($resolve, $reject, $notify) {
                $this->resolveCallback = $resolve;
                $this->rejectCallback  = $reject;
                $this->notifyCallback  = $notify;
            }, $this->canceller);
        }

        return $this->promise;
    }

    public function resolve($value = null)
    {
        $this->promise();

        call_user_func($this->resolveCallback, $value);
    }

    public function reject($reason = null)
    {
        $this->promise();

        call_user_func($this->rejectCallback, $reason);
    }

    public function notify($update = null)
    {
        $this->promise();

        call_user_func($this->notifyCallback, $update);
    }

```
    2. Promise对象
    
```
 public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null)
    {
        if (null !== $this->result) {
            return $this->result()->then($onFulfilled, $onRejected, $onProgress);
        }

        if (null === $this->canceller) {
            return new static($this->resolver($onFulfilled, $onRejected, $onProgress));
        }

        $this->requiredCancelRequests++;

        return new static($this->resolver($onFulfilled, $onRejected, $onProgress), function () {
            if (++$this->cancelRequests < $this->requiredCancelRequests) {
                return;
            }

            $this->cancel();
        });
    }
```
