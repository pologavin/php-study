> 异步编程即代码非同步执行的。可以归结为四种模式：  
     1. 回调；  
     2. 事件监听；  
     3. 发布/订阅；  
     4. promise模式；
    
### 异步实现方式
1. 线程方式
    如java，nginx 的多线程模型
2. 进程方式
    如php多进程模型
3. IO复用
    1. select
    2. poll
    3. epoll

### 优势和劣势
1. 优势：
    1. 解耦，你可以通过事件绑定，将复杂的业务逻辑分拆为多个事件处理逻辑
    2. 并发，结合非阻塞的IO，可以在单个进程（或线程）内实现对IO的并发访问；例如请求多个URL，读写多个文件等
    3. 效率，在没有事件机制的场景中，我们往往需要使用轮询的方式判断一个事件是否产生
    
2. 劣势：
    1. 可能有存在嵌套回调；代码可读性差
    2. 编写和调试复杂。


### 回调方式的异步
> 以回调函数的方式处理。程序是顺序执行，这种方式提供更好的扩展性和解耦。
1. 常见于php内置函数的回调方式

```
array_walk($arr, function($key, $value){
    $value += 1;
});
```

### 事件监听的异步
> 定时器，时间事件。
1. event扩展实现定时器

```
// 初始化一个EventConfig
$eventConfig = new EventConfig();
// 根据EventConfig初始化一个EventBase
$eventBase = new EventBase( $eventConfig );
// 初始化一个定时器event
$timer = new Event( $eventBase, -1, Event::TIMEOUT | Event::PERSIST, function(){
    echo microtime( true )." : 起飞！".PHP_EOL;
} );
// tick间隔为0.05秒钟，我们还可以改成0.5秒钟甚至0.001秒，也就是毫秒级定时器
$tick = 0.05;
// 将定时器event添加（可以不传 $tick）
$timer->add( $tick );
// eventBase进入loop状态
$eventBase->loop();
```
2. pcntl_signal信号器实现定时器

```
// 给当前php进程安装一个alarm信号处理器
// 当进程收到alarm时钟信号后会作出动作
pcntl_signal( SIGALRM, function(){
    echo "tick.".PHP_EOL;
} );
// 定义一个时钟间隔时间，1秒钟吧
$tick = 1;
while( true ){
    // 当过了tick时间后，向进程发送一个alarm信号
    pcntl_alarm( $tick );
    // 分发信号，呼唤起安装好的各种信号处理器
    pcntl_signal_dispatch();
    // 睡个1秒钟，继续
    sleep( $tick );
}
```

3. PHP有很多扩展和包提供了这方面的支持：

```
ext-libevent libevent扩展，基于libevent库，支持异步IO和时间事件

ext-event event扩展，支持异步IO和时间事件

ext-libev libev扩展，基于libev库，支持异步IO和时间事件

ext-eio eio扩展，基于eio库，支持磁盘异步操作

ext-swoole swoole扩展，支持异步IO和时间，方便编写异步socket服务器，推荐使用

package-react react包，提供了全面的异步编程方式，包括IO、时间事件、磁盘IO等等

package-workerman workerman包，类似swoole，php编写
```


### 发布订阅的异步
> 比如kafka


### promise的异步
> 意义在于解耦，就在刚刚我们提到的异步回调嵌套的问题，可以通过promise解决。其原理是在每一次传递回调函数的过程中，你都会拿到一个promise对象，而这个对象有一个then方法，then方法仍然可以返回一个promise对象，通过传递promise对象可以实现把多层嵌套分离出来。   
> https://github.com/reactphp/promise


```
function getJsonResult()
{
    return queryApi()
    ->then(
        // Transform API results to an object
        function ($jsonResultString) {
            return json_decode($jsonResultString);
        },
        // Transform API errors to an exception
        function ($jsonErrorString) {
            $object = json_decode($jsonErrorString);
            throw new ApiErrorException($object->errorMessage);
        }
    );
}
 
// Here we provide no rejection handler. If the promise returned has been
// rejected, the ApiErrorException will be thrown
getJsonResult()
->done(
    // Consume transformed object
    function ($jsonResultObject) {
        // Do something with $jsonResultObject
    }
);
```


### 异步编程开源项目
1. ampphp
> https://github.com/amphp/amp  
> 官网： https://amphp.org/


2. swoole
> https://github.com/swoole/swoole-src  
> 官网：https://www.swoole.com/

3. ReactPHP
> https://github.com/reactphp  
> 官网：https://reactphp.org/

4. workerman
> https://github.com/walkor/Workerman  
> 介绍： https://www.workerman.net/

5. Gearman
> https://github.com/gearman/gearmand  
> 介绍： https://wangying.sinaapp.com/archives/2157
```
Gearman应用场景：

1. 普通异步处理任务:订单处理,批量邮件,通知消息,群发短信,日志聚集
2. 高CPU或内存的异步处理任务:MapReduce分布式并行运算,视频编码转码处理,后台图片裁剪处理
3. 分布式和并行的处理任务：搜索,在线图片裁剪处理
4. 定时处理:增量更新,数据复制
5. 限制速率的FIFO处理：消息处理
6. 分布式的系统监控任务
```
