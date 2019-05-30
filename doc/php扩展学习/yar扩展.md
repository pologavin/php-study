- yar 是什么
1.  一个轻量级，高效的RPC框架。他提供一种简单方法让php项目之间可以互相远程调用对方本地方法。而且这种调用是可以并行的，可以支持同时调用多个远程服务的方法.
2. 是一种c/s的架构模式，客户机调用进程发送一个有进程参数的调用信息到服务进程，然后等待应答信息。在服务器端，进程保持睡眠状态直到调用信息的到达为止。当一个调用信息到达，服务器获得进程参数，计算结果，发送答复信息，然后等待下一个调用信息，最后，客户端调用进程接收答复信息，获得进程结果，然后调用执行继续进行。
3. 项目地址：https://github.com/laruence/yar

- yar 实现
1. 客户端

```
<?php

class RpcClient {
// RPC 服务地址映射表
public static $rpcConfig = array(
"RewardScoreService"    => "http://123.456.32.12/yar/server/RewardScoreService.class.php",
);

public static function init($server){
if (array_key_exists($server, self::$rpcConfig)) {
$uri = self::$rpcConfig[$server];
return new Yar_Client($uri);
}
}
}

$RewardScoreService = RpcClient::init("RewardScoreService");
var_dump($RewardScoreService->support(1, 2));

```
2. 客户端

```
<?php

class RewardScoreService {
/**
* $uid 给 $feedId 点赞
* @param $feedId  interge
* @param $uid  interge
* @return void
*/
public function support($uid,$feedId){
return "uid = ".$uid.", feedId = ".$feedId;
}
}

$yar_server = new Yar_server(new RewardScoreService());
$yar_server->handle();
```

- yar 实现原理
1. yar client是通过__call这个魔术方法来实现远程调用的，在Yar_client类里面并没有任何方法，当我们在调用一个不存在的方式的时候，就会执行__call方法，这个在框架中非常常见。


-  yar协议分析
1. 在 yar 中规定的传输协议如下图所示，请求体为82个字节的yar_header_t和8字节的打包名称和请求实体yar_request_t，在yar_header_t里面用body_len记录8字节的打包名称+请求实体的长度；返回体类似，只是实体内容的结构体稍微不同，在reval里面才是实际最后客户端需要的结果。**整个传输以二进制流的形式传送。**
![image](http://www.gavin.xin/wp-content/uploads/2018/08/企业微信截图_6aa7b2bb-294f-494b-acf3-f43dafe60ad8.png)
```
#ifdef PHP_WIN32
#pragma pack(push)
#pragma pack(1)
#endif
typedef struct _yar_header {
    unsigned int   id;
    unsigned short version;
    unsigned int   magic_num;
    unsigned int   reserved;
    unsigned char  provider[32];
    unsigned char  token[32];
    unsigned int   body_len; 
}
#ifndef PHP_WIN32
__attribute__ ((packed))
#endif
yar_header_t;
#ifdef PHP_WIN32
#pragma pack(pop)
#endif

yar_header_t * php_yar_protocol_parse(char *payload);
void php_yar_protocol_render(yar_header_t *header, uint id, char *provider, char *token, uint body_len, uint reserved);

#endif	/* PHP_YAR_PROTOCOL_H */

```

- Yar 数据传输的整体流程分析
1. 在yar_transport.h中，定义了yar_transport_t结构体

```
typedef struct _yar_transport_interface {
    void *data;
    int  (*open)(struct _yar_transport_interface *self, char *address, uint len, long options, char **msg TSRMLS_DC);
    int  (*send)(struct _yar_transport_interface *self, struct _yar_request *request, char **msg TSRMLS_DC);
    struct _yar_response * (*exec)(struct _yar_transport_interface *self, struct _yar_request *request TSRMLS_DC);
    int  (*setopt)(struct _yar_transport_interface *self, long type, void *value, void *addition TSRMLS_DC);
    int  (*calldata)(struct _yar_transport_interface *self, yar_call_data_t *calldata TSRMLS_DC);
    void (*close)(struct _yar_transport_interface *self TSRMLS_DC);
} yar_transport_interface_t;
 
 
typedef struct _yar_transport {
    const char *name;
    struct _yar_transport_interface * (*init)(TSRMLS_D);
    void (*destroy)(yar_transport_interface_t *self TSRMLS_DC);
    yar_transport_multi_t *multi;
} yar_transport_t;
```
2. 在transports目录下有两种传输协议，socket和curl，分别定义yar_transport_socket和yar_transport_curl

```
const yar_transport_t yar_transport_socket = {
	"sock",
	php_yar_socket_init,
	php_yar_socket_destroy,
	NULL
};

const yar_transport_t yar_transport_curl = {
	"curl",
	php_yar_curl_init,
	php_yar_curl_destroy,
	&yar_transport_curl_multi
};
```

3. 整体执行流程图
![image](http://www.gavin.xin/wp-content/uploads/2018/08/f7cfa22fddfdd0647c2809ce06db9ffe3d5ce644.jpeg)



- Yar 数据的打包和解包
1. 在yar_packager.c中首先定义了一个结构体，初始化的时候会把各个yar_packager_t注册到**packagers数组中。

```
struct _yar_packagers_list {
    unsigned int size;
    unsigned int num;
    yar_packager_t **packagers;
} yar_packagers_list;
typedef struct _yar_packager {
    const char *name;
    int  (*pack) (struct _yar_packager *self, zval *pzval, smart_str *buf, char **msg TSRMLS_DC);
    zval * (*unpack) (struct _yar_packager *self, char *content, size_t len, char **msg TSRMLS_DC);
} yar_packager_t;
```
2. 然后通过传入的name和yar_packager_t的name做比较，相同则返回该实例

```
PHP_YAR_API yar_packager_t * php_yar_packager_get(char *name, int nlen TSRMLS_DC) /* {{{ */ {
    int i = 0;
    for (;i<yar_packagers_list.num;i++) {
        if (strncasecmp(yar_packagers_list.packagers[i]->name, name, nlen) == 0) {
            return yar_packagers_list.packagers[i];
        }
    }
 
    return NULL;
} /* }}} */
```
