- 关于comet技术
> 常见的服务端推送技术有两种实现方式：一种是基于socket实现：采用RMI、CORBA或者自定义TCP/IP信息的applet来实现；一种是comet实现：基于 HTTP 长连接。comet是一种web推送技术，能使服务器能实时地将更新的信息传送到客户端，而无须客户端发出请求，有时也称为反向Ajax或服务器端推技术。

- 技术实现方式：
1. comet的精髓：
> 用服务器与javascript来维持浏览器的长连接，同时完成服务器端事件的浏览器端响应。这样的事件广播机制是跨网络的，同时也是实时的。

2. 两种实现方式：
    1. 基于ajax的长轮询：
        > 由客户端发送以个ajax连接，服务端一直处理等待数据更新后才返回客户端结束一次连接，客户端处理完后就马上再次发送同样的ajax连接。就这样的不停的处于长连接状态。
    2. 基于iframe及htmlfile流的方式：
        > 在页面中插入一个隐藏的iframe，利用其src属性在服务器和客户端之间建立一条长链接，服务器向iframe传输数据（通常是HTML，内有负责插入信息的javascript），来实时更新页面。  iframe流方式的优点是浏览器兼容好，Google公司在一些产品中使用了iframe流，如Google Talk。


- comet技术的优缺点：
1. 优点：实时性好（消息延时小）；性能好（能支持大量用户）
2. 缺点：长期占用连接，丧失了无状态高并发的特点


- php实现demo

```
<?php
// 设置请求运行时间不限制，解决因为超过服务器运行时间而结束请求
ini_set("max_execution_time", "0");
 
$filename  = dirname(__FILE__).'/data.txt';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
 
// 判断页面提交过来的修改内容是否为空，不为空则将内容写入文件，并中断流程
if ($msg != '')
{
    file_put_contents($filename,$msg);
    exit;
}
 
/* 获取文件上次修改时间戳 和 当前获取到的最近一次文件修改时间戳
 * 文件上次修改时间戳 初始 默认值为0
 * 最近一次文件修改时间戳 通过 函数 filemtime()获取
 */
$lastmodif    = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;
clearstatcache();  // 清除文件状态缓存
$currentmodif = filemtime($filename);
 
/* 如果当前返回的文件修改unix时间戳小于或等于上次的修改时间，
 * 表明文件没有更新不需要推送消息
 * 如果当前返回的文件修改unix时间戳大于上次的修改时间
 * 表明文件有更新需要输出修改的内容作为推送消息
 */
while ($currentmodif <= $lastmodif)
{
    usleep(10000);     // 休眠10ms释放cpu的占用
    clearstatcache();  // 清除文件状态缓存
    $currentmodif = filemtime($filename);
}
 
// 推送信息处理(需要推送说明文件有更改，推送信息包含本次修改时间、内容)
$response = array();
$response['msg'] = file_get_contents($filename);
$response['timestamp'] = $currentmodif;
echo json_encode($response);
flush();
?>
```
