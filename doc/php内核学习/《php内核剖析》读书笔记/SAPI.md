SAPI(Server Application Programming Interface）服务端应用编程接口，他是php框架中跟应用层交互的接口层；是php内部入口。php脚本的执行都是从SAPI接口开始。

- Cli 命令行接口
> 用于在命令行模式执行php脚本。
1. 执行方式：

```
php [option] [php文件]

// 常用参数：
1. -a: 交互式执行，即进入php>[php code] 回车即可执行输出结果；
2. -c [php.ini path]: 读入执行php.ini文件执行；
3. -f [file]: 读入并解释指定的文件
4. -e: 输出额外信息以便调试和性能分析
5. -i: 输出php相关信息；
6. -m: 输出引用的模块；
7. -r [PHP code] 运行执行php代码；
8. -z [fiel]: 调入zend 扩展文件
9. --ini: 输出配置文件名；
```
cli是一种单进程的模式，处理完请求即直接关闭进程。他是一种较为简单的完整的php生命周期。
2. 执行流程：

```
main() -> php_cli_startup() -> do_cli() -> php_module_shutdown() 
```

- Fpm(FastCGI Process Manager) PHP FastCGI模式的进程管理器。
1. FastCGI(Fast Common Gateway Interface)
>  快速通用网关接口，一种web服务器（nginx/apache）和处理程序之间的一种通讯协议。跟HTTP类似是一种应用层通信协议。
2. 处理流程
> 在网络应用场景下，使用FastCGI协议与web服务器配合实现了HTTP的处理，web服务器来处理HTTP请求，然后将解析的结果再通过FastCGI协议转发给处理程序，处理程序处理完成后将结果返回给web服务器，web服务器再返回给客户端。
3. 基本实现
> 1. 多进程模型：由一个master进程和多个worker进程组成，master进程启动时会创建一个socket，但是不会接收，处理请求，而是由fork出的worker子进程完成请求的接收和处理；
> 2. master进程：管理worker进程，负责fork或者worker进程；
> 3. worker进程：处理请求，每个worker进程会竞争地Accept请求，接收成功后解析FastCGI，然后执行相应的脚本，处理完成后关闭请求，继续等待新的连接。一个worker进程只能处理一个请求，所以没有所谓并发导致的资源冲突问题。
4. 进程间通讯：master进程和worker进程之间不会直接进行通讯，master进程是通过共享内存获取worker进程的信息。
5. 常用配置
> 1. 进程分配  
        1. pm = static 模式：创建的php-fpm子进程数量是固定的，那么就只有pm.max_children = 50这个参数生效；  
        2. pm = dynamic 模式：示启动进程是动态分配的，随着请求量动态变化的。他由 pm.max_children（最大可创建的子进程的数量），pm.start_servers（随着php-fpm一起启动时创建的子进程数目），pm.min_spare_servers（服务器空闲时最小php-fpm进程数量），pm.max_spare_servers （服务器空闲时最大php-fpm进程数量）这几个参数共同决定。  

动态适合小内存机器，灵活分配进程，省内存。静态适用于大内存机器，动态创建回收进程对服务器资源也是一种消耗。        
> 2. 慢日志查询  
    php-fpm慢日志会记录下进程号，脚本名称，具体哪个文件哪行代码的哪个函数执行时间过长

```
slowlog = /usr/local/var/log/php-fpm.log.slow
request_slowlog_timeout = 15s
// 当一个请求该设置的超时时间15秒后，就会将对应的PHP调用堆栈信息完整写入到慢日志中。
```

- Embed
> 为其他c/c++等语言应用中调用php提供的API。编译php时通过“--enable-embed=[shared|static]”指定库类型，默认是共享库。编译完成后可以在php安装位置的lib目录下看到生成的库文件，同时在“include/php/sapi”目录下生成一个存放Embed头文件的目录。
1. 实现
> 实现原理：将php生命周期的几个处理函数进行封装，对外提供两个接口"php_embed_init()"和“php_embed_shutdown”。




