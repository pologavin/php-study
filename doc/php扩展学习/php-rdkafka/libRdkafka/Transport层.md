> 与kafka集群通讯网络传输层。

1. librdkafka是访问kafka集群的客户端。需要支持大并发, 在网络IO模型上选用了poll;
2. 发送和接收数据必不可少的缓冲区buffer
3. Librdkafka与kafka broker间是tcp连接, 在接收数据后就涉及到一个拆包组包的问题, 这个就和kafka的协议有关了, kafka的二进制协议:
    1. 前4个字节是payload长度;
    2. 后面紧接着是payload具体内容,这部分又分为header和body两部分;

### kafka socket相关配置

配置项 | 配置值范围 | 默认值 | 描述
---|---|---|---
socket.timeout.ms | 10 ~ 300000 | 60000 | 默认的网络请求超时时间, Producer：ProduceRequests将使用批处理中第一条消息的socket.timeout.ms和其余message.timeout.ms的较小值。 使用者：FetchRequests将使用fetch.wait.max.ms + socket.timeout.ms. Type: integer
socket.blocking.max.ms | 1 ~ 60000 | 1000 | socket套接字可能阻塞的最大时间，比较小的值提高了响应速度，但是CPU负载比较大. 已废弃. Deprecated Type: integer
socket.send.buffer.bytes | 0 ~ 100000000 | 0 | broker端发送缓冲区大小，0则使用系统默认值. Type: integer
socket.receive.buffer.bytes | 0 ~ 100000000 | 0 | broker端接收缓冲区大小，0则使用系统默认值. Type: integer
socket.keepalive.enable	| true, false | false 默认短连接 | 启用TCP keep-alives (SO_KEEPALIVE) on broker sockets  Type: boolean
socket.nagle.disable | true, false | false | 禁用nagle 算法TCP_NODELAY Type: boolean
socket.max.fails | 0 ~ 1000000 | 1 | 发送失败的最大次数，超过该次数后断开与broker的连接，0 禁用；注意：连接会自动重连. Type: integer
socket_cb | 无 | 无 | 为socket套接字创建CLOEXEC提供回调函数 Type: pointer
connect_cb | 无 | 无 | socket关闭的回调函数 Type: pointer
closesocket_cb | 无 | 无 | socket关闭的回调函数 Type: pointer



