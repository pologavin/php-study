> RdKafka\Queue的实现。

```
INIT_NS_CLASS_ENTRY(ce, "RdKafka", "Queue", kafka_queue_fe);
static const zend_function_entry kafka_queue_fe[] = {
    PHP_ME(RdKafka, __construct, arginfo_kafka___private_construct, ZEND_ACC_PRIVATE)
    PHP_ME(RdKafka__Queue, consume, arginfo_kafka_queue_consume, ZEND_ACC_PUBLIC)
    PHP_FE_END
};
```

- RdKafka\Queue
1. Queue::consume ( string $timeout_ms )
> 按queue消费消息，跟ConsumerTopic::consumeQueueStart相对。同ConsumerTopic::consume()。

```
PHP_METHOD(RdKafka__Queue, consume)
{
    ...
    message = rd_kafka_consume_queue(intern->rkqu, timeout_ms);
    
    kafka_message_new(return_value, message TSRMLS_CC);

    rd_kafka_message_destroy(message);
    ...
}
```
