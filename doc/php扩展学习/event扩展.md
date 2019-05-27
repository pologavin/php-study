### 是什么
>  1. 基于时间和事件驱动的扩展。
>  2. 有效安排I/O；
>  3. 可用于特定平台的最佳I/O通知机制的事件

### 安装
1. 安装libevent

```
wget -c https://github.com/libevent/libevent/releases/download/release-2.1.8-stable/libevent-2.1.8-stable.tar.gz -P /tmp

cd /tmp 
tar -zxvf libevent-2.1.8-stable.tar.gz && cd libevent-2.1.8-stable
./configure --prefix=/usr/local/libevent-2.1.8
make && make install

```
2. 安装event

```
pecl install event

// 如果package 不存在。
// 先下载http://pecl.php.net/get/event-2.3.0.tgz
再执行pecl install event-2.3.0.tgz

记得选择正确的lievent perfix 和openssl perfix

lievent perfix: /usr/local/libevent-2.1.8
openssl perfix:/usr/local/openssl
```
3. 开启event扩展

```
docker-php-ext-enable sockets
docker-php-ext-enable event

// 验证
php -m | grep event

// 注意：event扩展必须开启sockets扩展。编译可以加上参数 --enable-sockets
// 或者单独开启docker-php-ext-enable sockets


```
