1. 设置用户自定义会话存储函数,用来设置用户级session存储函数，用于存储和取回session相关的数据，用于那些使用不同的php session指定的存储方式的情况。
> php的相关文档：http://php.net/manual/zh/function.session-set-save-handler.php


2. php5.4之前版本：

```
bool session_set_save_handler ( callable $open , callable $close , callable $read , callable $write , callable $destroy , callable $gc [, callable $create_sid [, callable $validate_sid [, callable $update_timestamp ]]] )
```


3. php5.4+版本：

```
bool session_set_save_handler ( object $sessionhandler [, bool $register_shutdown = TRUE ] )
```

