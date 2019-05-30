> RdKafka\topic, RdKafka\ConsumerTopic, RdKafka\KafkaConsumerTopic, RdKafka\ProducerTopic 的实现。


```
// RdKafka\topic
INIT_NS_CLASS_ENTRY(ce, "RdKafka", "Topic", kafka_topic_fe);
static const zend_function_entry kafka_topic_fe[] = {
    PHP_ME(RdKafka__Topic, getName, arginfo_kafka_topic_get_name, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

// RdKafka\ConsumerTopic
INIT_NS_CLASS_ENTRY(ce, "RdKafka", "ConsumerTopic", kafka_consumer_topic_fe);
static const zend_function_entry kafka_consumer_topic_fe[] = {
    PHP_ME(RdKafka, __construct, arginfo_kafka___private_construct, ZEND_ACC_PRIVATE)
    PHP_ME(RdKafka__ConsumerTopic, consumeQueueStart, arginfo_kafka_consume_queue_start, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__ConsumerTopic, consumeStart, arginfo_kafka_consume_start, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__ConsumerTopic, consumeStop, arginfo_kafka_consume_stop, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__ConsumerTopic, consume, arginfo_kafka_consume, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__ConsumerTopic, offsetStore, arginfo_kafka_offset_store, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

// RdKafka\KafkaConsumerTopic
INIT_NS_CLASS_ENTRY(ce, "RdKafka", "KafkaConsumerTopic", kafka_kafka_consumer_topic_fe);
static const zend_function_entry kafka_kafka_consumer_topic_fe[] = {
    PHP_ME(RdKafka, __construct, arginfo_kafka___private_construct, ZEND_ACC_PRIVATE)
    PHP_ME(RdKafka__ConsumerTopic, offsetStore, arginfo_kafka_offset_store, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

// RdKafka\ProducerTopic
INIT_NS_CLASS_ENTRY(ce, "RdKafka", "ProducerTopic", kafka_producer_topic_fe);
static const zend_function_entry kafka_producer_topic_fe[] = {
    PHP_ME(RdKafka, __construct, arginfo_kafka___private_construct, ZEND_ACC_PRIVATE)
    PHP_ME(RdKafka__ProducerTopic, produce, arginfo_kafka_produce, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

```

- RdKafka\topic 
1. Topic::getName()
> 获取topic的名字

```
PHP_METHOD(RdKafka__Topic, getName)
{
    ...
    intern = get_kafka_topic_object(getThis() TSRMLS_CC);
    if (!intern) {
        return;
    }
    // 获取该topic的名字
    RDKAFKA_RETURN_STRING(rd_kafka_topic_name(intern->rkt));
}
```


- RdKafka\ConsumerTopic
1. ConsumerTopic::consumeQueueStart ( integer $partition , integer $offset , RdKafka\Queue $queue )
> 将分区传送到队列。同ConsumerTopic::consumeStart() ，区别是这个方法会将传入的消息重新路由到提供的队列。必须使用Queue::consume*() 之一接收消息。


```
PHP_METHOD(RdKafka__ConsumerTopic, consumeQueueStart)
{
    ...
     kafka_queue_object *queue_intern;
    long partition;
    long offset;
    
    //将分区传送到队列
    ret = rd_kafka_consume_start_queue(intern->rkt, partition, offset, queue_intern->rkqu);
    ...
}
```

2. ConsumerTopic::consumeStart ( integer $partition , integer $offset )
> 从一个分区开始消费消息。
    offset 可以特定值：  
    1. RD_KAFKA_OFFSET_BEGINNING  
    2. RD_KAFKA_OFFSET_END  
    3. RD_KAFKA_OFFSET_STORED  
    4. The return value of rd_kafka_offset_tail()  

```
PHP_METHOD(RdKafka__ConsumerTopic, consumeStart)
{
    ...
    long partition;
    long offset;
    
    // 开始接收消息api（低级api）
    ret = rd_kafka_consume_start(intern->rkt, partition, offset);
    ...
}

```

3. ConsumerTopic::consumeStop ( integer $partition )
> 停止消费一个分区的消息。

```
PHP_METHOD(RdKafka__ConsumerTopic, consumeStop)
{
    ...
    long partition;
    
    // 停止接收（低级api）
    ret = rd_kafka_consume_stop(intern->rkt, partition);
    ...
}
```

4. ConsumerTopic::consume ( integer $partition , integer $timeout_ms )
> 从一个分区消费单条消息。

```
PHP_METHOD(RdKafka__ConsumerTopic, consume)
{
    ...
    long partition;
    long timeout_ms;
    
    //接收一条message（低级api）
    message = rd_kafka_consume(intern->rkt, partition, timeout_ms);
    
    // 如果返回null，错误码是超时也返回空（坑）
    if (!message) {
        err = rd_kafka_errno2err(errno);
        if (err == RD_KAFKA_RESP_ERR__TIMED_OUT) {
            return;
        }
        zend_throw_exception(ce_kafka_exception, rd_kafka_err2str(err), err TSRMLS_CC);
        return;
    }
    
    kafka_message_new(return_value, message TSRMLS_CC);
    ...
}
```

5. ConsumerTopic::offsetStore ( integer $partition , integer $offset )
> 手动存储offset。

```
PHP_METHOD(RdKafka__ConsumerTopic, offsetStore)
{
    ...
    long partition;
    long offset;
    
    // 手动commit offset
    err = rd_kafka_offset_store(intern->rkt, partition, offset);
    ...
}
```

- RdKafka\KafkaConsumerTopic
1. KafkaConsumerTopic::offsetStore ( integer $partition , integer $offset )
> 同ConsumerTopic::offsetStore。


- RdKafka\ProducerTopic
1. ProducerTopic::produce ( integer $partition , integer $msgflags , string $payload [, string $key = NULL ] )
> 生产和发送单条消息。
>    1. partition 参数可以为RD_KAFKA_PARTITION_UA，也可以TopicConf::setPartitioner()设置。
>    2. key参数，如果partition为RD_KAFKA_PARTITION_UA，且key不为空，即使用key值hash和分区数取模计算出分区编号；    


```
PHP_METHOD(RdKafka__ProducerTopic, produce)
{
    ...
    long partition;
    long msgflags;
    char *payload;
    arglen_t payload_len;
    char *key = NULL;
    arglen_t key_len = 0;
    
    // 发送一条消息
    ret = rd_kafka_produce(intern->rkt, partition, msgflags | RD_KAFKA_MSG_F_COPY, payload, payload_len, key, key_len, NULL);
    ...
}
```

