
```
boolean fastcgi_finish_request ( void )
```

- 函数功能
1. 冲刷(flush)所有响应的数据给客户端并结束请求。 这使得客户端结束连接后;
2. 需要大量时间运行的任务能够继续运行。


