### kafkaConsumer实现


```
static const zend_function_entry fe[] = { /* {{{ */
    PHP_ME(RdKafka__KafkaConsumer, __construct, arginfo_kafka_kafka_consumer___construct, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, assign, arginfo_kafka_kafka_consumer_assign, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, getAssignment, arginfo_kafka_kafka_consumer_getAssignment, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, commit, arginfo_kafka_kafka_consumer_commit, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, commitAsync, arginfo_kafka_kafka_consumer_commit_async, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, consume, arginfo_kafka_kafka_consumer_consume, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, subscribe, arginfo_kafka_kafka_consumer_subscribe, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, getSubscription, arginfo_kafka_kafka_consumer_getSubscription, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, unsubscribe, arginfo_kafka_kafka_consumer_unsubscribe, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, getMetadata, arginfo_kafka_kafka_consumer_getMetadata, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__KafkaConsumer, newTopic, arginfo_kafka_kafka_consumer_new_topic, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

INIT_NS_CLASS_ENTRY(tmpce, "RdKafka", "KafkaConsumer", fe);

```

1. KafkaConsumer::__construct( RdKafka\Conf $conf )

```
PHP_METHOD(RdKafka__KafkaConsumer, __construct)
{
    ...
    // kafka的创建
    rk = rd_kafka_new(RD_KAFKA_CONSUMER, conf, errstr, sizeof(errstr));
    ...
    // 将接收到 queue 从rd_kafka_poll()重定向到rd_kafka_poll_set_consumer()
    rd_kafka_poll_set_consumer(rk);
    ...
}
```

2. KafkaConsumer::assign ([ array $topic_partitions = NULL ] )

```
PHP_METHOD(RdKafka__KafkaConsumer, assign)
{
    ...
    rd_kafka_topic_partition_list_t *topics;
     
    //分派partition,跟rd_kafka_subscribe()的区别就是assign必须有partition才生效，跟rd_kafka_subscribe是自动分配partition
    err = rd_kafka_assign(intern->rk, topics);
    ...
}
```

3. KafkaConsumer::getAssignment()

```
PHP_METHOD(RdKafka__KafkaConsumer, getAssignment)
{
    ...
    rd_kafka_topic_partition_list_t *topics;
    //获取当前分派的partition
    err = rd_kafka_assignment(intern->rk, &topics);
    ...
}
```

4. KafkaConsumer::subscribe ( array $topics )

```
    ...
    rd_kafka_topic_partition_list_t *topics;
    //订阅topic
    err = rd_kafka_subscribe(intern->rk, topics);
    ...
```

5. KafkaConsumer::getSubscription()

```
PHP_METHOD(RdKafka__KafkaConsumer, getSubscription)
{
    ...
    rd_kafka_topic_partition_list_t *topics;
    
    //获取当前订阅 Returns an array of topic names
    err = rd_kafka_subscription(intern->rk, &topics);
    ...
}
```

6. KafkaConsumer::unsubscribe()

```
PHP_METHOD(RdKafka__KafkaConsumer, unsubscribe)
{
    ...
    //取消订阅
    err = rd_kafka_unsubscribe(intern->rk);
    ...
}
```

7. KafkaConsumer::consume ( string $timeout_ms )

```
PHP_METHOD(RdKafka__KafkaConsumer, consume)
{
    ...
    long timeout_ms;
    
    //高级api consumer 自动负载均衡partition，只支持单条消费
    rkmessage = rd_kafka_consumer_poll(intern->rk, timeout_ms);
    
    // 如果返回null或者空，即默认返回超时错误码（坑）
    if (!rkmessage) {
        rkmessage_tmp.err = RD_KAFKA_RESP_ERR__TIMED_OUT;
        rkmessage = &rkmessage_tmp;
    }

    kafka_message_new(return_value, rkmessage TSRMLS_CC);
    ...
}
```

8. KafkaConsumer::commit ([ mixed $message_or_offsets = NULL ] )

```
PHP_METHOD(RdKafka__KafkaConsumer, commit)
{
    ...
    rd_kafka_topic_partition_list_t *offsets = NULL;
    //同步commit
    int async =0;
    //手动commit，同步/异步，异步的时候要设置rd_kafka_conf_set_offset_commit_cb();
    err = rd_kafka_commit(intern->rk, offsets, async);
    ...
}
```

9. KafkaConsumer::commitAsync ([ string $message_or_offsets = NULL ] )

```
PHP_METHOD(RdKafka__KafkaConsumer, commitAsync)
{
    ...
    rd_kafka_topic_partition_list_t *offsets = NULL;
    //异步commit
    int async =1;
    //手动commit，同步/异步，异步的时候要设置rd_kafka_conf_set_offset_commit_cb();
    err = rd_kafka_commit(intern->rk, offsets, async);
    ... 
}
```

10. KafkaConsumer::getMetadata ( bool $all_topics , RdKafka\KafkaConsumerTopic $only_topic = NULL , int $timeout_ms )

```
PHP_METHOD(RdKafka__KafkaConsumer, getMetadata)
{
    ...
    zend_bool all_topics;
    long timeout_ms;
    const rd_kafka_metadata_t *metadata;
    
    // 获取元信息metadata
    err = rd_kafka_metadata(intern->rk, all_topics, only_orkt ? only_orkt->rkt : NULL, &metadata, timeout_ms);
    ...
}
```

11. KafkaConsumer::newTopic( string $topic_name [, RdKafka\TopicConf $topic_conf = NULL ] )
> 这个方法同RdKafka::newTopic，一般使用RdKafka的

```
PHP_METHOD(RdKafka__KafkaConsumer, newTopic)
{
    ...
    char *topic;
    rd_kafka_topic_conf_t *conf = NULL;
    
    // 创建topic
    rkt = rd_kafka_topic_new(intern->rk, topic, conf);

    ...
}
```




