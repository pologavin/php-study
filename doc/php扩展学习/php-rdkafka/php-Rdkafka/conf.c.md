> RdKafka\Conf  和  RdKafka\TopicConf 的实现

```
zend_class_entry * ce_kafka_conf;
zend_class_entry * ce_kafka_topic_conf;
```

### 其配置项
> https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md

- Conf类

```
// __construct
PHP_METHOD(RdKafka__Conf, __construct)
{
    ...
    intern->type = KAFKA_CONF;
    intern->u.conf = rd_kafka_conf_new();
    ...
}

// dump 复制conf
PHP_METHOD(RdKafka__Conf, dump)
{
    ...
     switch (intern->type) {
        case KAFKA_CONF:
            dump = rd_kafka_conf_dump(intern->u.conf, &cntp);
            break;
        case KAFKA_TOPIC_CONF:
            dump = rd_kafka_topic_conf_dump(intern->u.topic_conf, &cntp);
            break;
        default:
            return;
    }
    ...
}


// set 设置conf 配置项
// 参数： string $name 配置项名 , string $value  配置值
PHP_METHOD(RdKafka__Conf, set)
{
    ...
    switch (intern->type) {
        case KAFKA_CONF:
            ret = rd_kafka_conf_set(intern->u.conf, name, value, errstr, sizeof(errstr));
            break;
        case KAFKA_TOPIC_CONF:
            ret = rd_kafka_topic_conf_set(intern->u.topic_conf, name, value, errstr, sizeof(errstr));
            break;
    }
    ...
}


// 错误回调函数：
// 参数callback
PHP_METHOD(RdKafka__Conf, setErrorCb)
{
    ...
    rd_kafka_conf_set_error_cb(intern->u.conf, kafka_conf_error_cb);
    ...
}


// 发送消息回调函数：
// 参数：callback
PHP_METHOD(RdKafka__Conf, setDrMsgCb)
{
    ...
    rd_kafka_conf_set_dr_msg_cb(intern->u.conf, kafka_conf_dr_msg_cb);
    ...
}


// 统计回调函数：
// 参数：callback
PHP_METHOD(RdKafka__Conf, setStatsCb)
{
    ...
    rd_kafka_conf_set_stats_cb(intern->u.conf, kafka_conf_stats_cb);
    ...
}

// 重新负载均衡回调函数：
PHP_METHOD(RdKafka__Conf, setRebalanceCb)
{
    ...
    rd_kafka_conf_set_rebalance_cb(intern->u.conf, kafka_conf_rebalance_cb);
    ...
}

// 接收消息回调函数：
PHP_METHOD(RdKafka__Conf, setConsumeCb) 
{
    ...
    rd_kafka_conf_set_consume_cb(intern->u.conf, kafka_conf_consume_cb);
    ...
}

// commit 回调函数：
PHP_METHOD(RdKafka__Conf, setOffsetCommitCb)
{
    ...
    rd_kafka_conf_set_offset_commit_cb(intern->u.conf, kafka_conf_offset_commit_cb);
    ...
}

```


- topicConf类

```
// __construct
PHP_METHOD(RdKafka__TopicConf, __construct)
{
    ...
    intern->type = KAFKA_TOPIC_CONF;
    intern->u.topic_conf = rd_kafka_topic_conf_new();
    ...
}

// 设置partitioner
PHP_METHOD(RdKafka__TopicConf, setPartitioner)
{
    ...
    rd_kafka_topic_conf_set_partitioner_cb(intern->u.topic_conf, partitioner);
    ...
}

// set 和dump 同conf下的方法。

```
