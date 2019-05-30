> RdKafka\TopicPartition的实现。


```
INIT_NS_CLASS_ENTRY(tmpce, "RdKafka", "TopicPartition", fe);
static const zend_function_entry fe[] = { /* {{{ */
    PHP_ME(RdKafka__TopicPartition, __construct, arginfo_kafka_topic_partition___construct, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, getTopic, arginfo_kafka_topic_partition_get_topic, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, setTopic, arginfo_kafka_topic_partition_set_topic, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, getPartition, arginfo_kafka_topic_partition_get_partition, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, setPartition, arginfo_kafka_topic_partition_set_partition, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, getOffset, arginfo_kafka_topic_partition_get_offset, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__TopicPartition, setOffset, arginfo_kafka_topic_partition_set_offset, ZEND_ACC_PUBLIC)
    PHP_FE_END
};
```

1. TopicPartition::__construct ( string $topic , integer $partition [, integer $offset = NULL ] )
> topicPartition 初始化。

```
PHP_METHOD(RdKafka__TopicPartition, __construct)
{
    ...
    kafka_topic_partition_init(getThis(), topic, partition, offset TSRMLS_CC);
    ...
}

void kafka_topic_partition_init(zval *zobj, char * topic, int32_t partition, int64_t offset TSRMLS_DC) /* {{{ */
{
    object_intern *intern;

    intern = get_custom_object_zval(object_intern, zobj);
    if (!intern) {
        return;
    }

    if (intern->topic) {
        efree(intern->topic);
    }
    intern->topic = estrdup(topic);

    intern->partition = partition;
    intern->offset = offset;
}
```

2. TopicPartition::getTopic ()
> 获取topic_name。

```
PHP_METHOD(RdKafka__TopicPartition, getTopic)
{
    ...
    RDKAFKA_RETURN_STRING(intern->topic);
    ...
}
```

3. TopicPartition::setTopic ( string $topic_name ) 
> 设置topic_name。

```
PHP_METHOD(RdKafka__TopicPartition, setTopic)
{
    ...
    intern->topic = estrdup(topic);
    ...
}
```

4. TopicPartition::getPartition()
> 获取partition ID。

```
PHP_METHOD(RdKafka__TopicPartition, getPartition)
{
    ...
    RETURN_LONG(intern->partition);
    ...
}
```

5. TopicPartition::setPartition ( string $partition )
> 设置partition ID。

```
PHP_METHOD(RdKafka__TopicPartition, setPartition)
{
    ...
    long partition;
    
    intern->partition = partition;
    ...
}
```

6. TopicPartition::getOffset ()
> 获取offset。

```
PHP_METHOD(RdKafka__TopicPartition, getOffset)
{
    ...
    RETURN_LONG(intern->offset);
    ...
}
```

7. TopicPartition::setOffset ( string $offset )
> 设置offset。

```
PHP_METHOD(RdKafka__TopicPartition, setOffset)
{
    ...
    long offset;
    
    intern->offset = offset;
    ...
}
```
