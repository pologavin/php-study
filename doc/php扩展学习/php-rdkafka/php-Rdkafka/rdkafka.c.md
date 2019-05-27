> RdKafka, RdKafka\Consumer, RdKafka\Producer, RdKafka\Exception的实现。


```
INIT_CLASS_ENTRY(ce, "RdKafka", kafka_fe);
static const zend_function_entry kafka_fe[] = {
    PHP_ME(RdKafka__Kafka, addBrokers, arginfo_kafka_add_brokers, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Kafka, getMetadata, arginfo_kafka_get_metadata, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Kafka, getOutQLen, arginfo_kafka_get_outq_len, ZEND_ACC_PUBLIC)
    PHP_MALIAS(RdKafka__Kafka, metadata, getMetadata, arginfo_kafka_get_metadata, ZEND_ACC_PUBLIC | ZEND_ACC_DEPRECATED)
    PHP_ME(RdKafka__Kafka, setLogLevel, arginfo_kafka_set_log_level, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Kafka, newQueue, arginfo_kafka_new_queue, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Kafka, newTopic, arginfo_kafka_new_topic, ZEND_ACC_PUBLIC)
    PHP_MALIAS(RdKafka__Kafka, outqLen, getOutQLen, arginfo_kafka_get_outq_len, ZEND_ACC_PUBLIC | ZEND_ACC_DEPRECATED)
    PHP_ME(RdKafka__Kafka, poll, arginfo_kafka_poll, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Kafka, setLogger, arginfo_kafka_set_logger, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

INIT_NS_CLASS_ENTRY(ce, "RdKafka", "Consumer", kafka_consumer_fe);
static const zend_function_entry kafka_consumer_fe[] = {
    PHP_ME(RdKafka__Consumer, __construct, arginfo_kafka_consumer___construct, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

INIT_NS_CLASS_ENTRY(ce, "RdKafka", "Producer", kafka_producer_fe);
static const zend_function_entry kafka_producer_fe[] = {
    PHP_ME(RdKafka__Producer, __construct, arginfo_kafka_producer___construct, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

INIT_NS_CLASS_ENTRY(ce, "RdKafka", "Exception", NULL);

```

- RdKafka
1. RdKafka::addBrokers ( string $broker_list )
> 添加brokers，$broker_list用逗号分隔。

```
PHP_METHOD(RdKafka__Kafka, addBrokers)
{
    ...
    char *broker_list;
    
    intern = get_kafka_object(getThis() TSRMLS_CC);
    
    //添加brokers
    RETURN_LONG(rd_kafka_brokers_add(intern->rk, broker_list));
    ...
}
```

2. RdKafka::getMetadata ( bool $all_topics , RdKafka\Topic $only_topic = NULL , int $timeout_ms )
> 从broker获取元信息。同KafkaConsumer::getMetadata。

```
PHP_METHOD(RdKafka__Kafka, getMetadata)
{
    ...
    // 获取metadata
    err = rd_kafka_metadata(intern->rk, all_topics, only_orkt ? only_orkt->rkt : NULL, &metadata, timeout_ms);
    
    kafka_metadata_init(return_value, metadata TSRMLS_CC)
    ...
}
```

3. RdKafka::setLogLevel ( integer $level ) 
> 设置日志等级。

```
PHP_METHOD(RdKafka__Kafka, setLogLevel)
{
    ...
    // 设置日志等级
    rd_kafka_set_log_level(intern->rk, level);
    ...
}
```

4. RdKafka::newQueue ()
> 创建一个新的message queue。

```
PHP_METHOD(RdKafka__Kafka, newQueue)
{
    ...
    // 创建queue
    rkqu = rd_kafka_queue_new(intern->rk);
    ...
}
```

5. RdKafka::newTopic ( string $topic_name [, RdKafka\TopicConf $topic_conf = NULL ] )
> 创建新的topic。同KafkaConsumer::newTopic。

```
PHP_METHOD(RdKafka__Kafka, newTopic)
{
    ...
    // 创建topic
    rkt = rd_kafka_topic_new(intern->rk, topic, conf);
    ...
}
```

6. RdKafka::getOutQLen()
> 获取队列长度。

```
PHP_METHOD(RdKafka__Kafka, getOutQLen)
{
    ...
    // 队列长度
    RETURN_LONG(rd_kafka_outq_len(intern->rk));
    ...
}
```

7. RdKafka::poll( integer $timeout_ms )
> 对事件响应。阻塞等待消息发送完成

```
PHP_METHOD(RdKafka__Kafka, poll)
{
    ...
    // 响应事件
    RETURN_LONG(rd_kafka_poll(intern->rk, timeout));
    ...
}
```

8. RdKafka::setLogger()
> 设置日志。

```
PHP_METHOD(RdKafka__Kafka, setLogger)
{
    ...
    rd_kafka_set_logger(intern->rk, logger);
    ...
}
```

- RdKafka\Producer
1. Producer::__construct ([ RdKafka\Conf $conf = NULL ] )
> 初始化Producer。

```
PHP_METHOD(RdKafka__Producer, __construct)
{
    ...
    kafka_init(getThis(), RD_KAFKA_PRODUCER, zconf TSRMLS_CC);
    ...
}

static void kafka_init(zval *this_ptr, rd_kafka_type_t type, zval *zconf TSRMLS_DC)
{
  ...
  rk = rd_kafka_new(type, conf, errstr, sizeof(errstr));
  ...
}
```

- RdKafka\Consumer
1. Consumer::__construct ([ RdKafka\Conf $conf = NULL ] )
> 初始化consumer。

```
PHP_METHOD(RdKafka__Consumer, __construct)
{
    ...
    kafka_init(getThis(), RD_KAFKA_CONSUMER, zconf TSRMLS_CC);
    ...
}
```

