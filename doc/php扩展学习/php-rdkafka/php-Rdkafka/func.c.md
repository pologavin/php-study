> kafka function 


```
// 获取所有错误描述列表
PHP_FUNCTION(rd_kafka_get_err_descs)
{
    ...
    rd_kafka_get_err_descs(&errdescs, &cnt);
    ...
}

// 获取单个错误的描述
PHP_FUNCTION(rd_kafka_err2str)
{
    ...
    errstr = rd_kafka_err2str(err);
    ...
}

// 获取系统错误码
PHP_FUNCTION(rd_kafka_errno)
{
    
}

//获取kafka错误码
PHP_FUNCTION(rd_kafka_errno2err)
{
    
}

// 返回指定offset
PHP_FUNCTION(rd_kafka_offset_tail)
{
    
}

```

