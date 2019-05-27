> RdKafka\Metadata的实现。


```
INIT_NS_CLASS_ENTRY(tmpce, "RdKafka", "Metadata", kafka_metadata_fe);
static const zend_function_entry kafka_metadata_fe[] = {
    PHP_ME(RdKafka__Metadata, getOrigBrokerId, arginfo_kafka_metadata_get_orig_broker_id, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Metadata, getOrigBrokerName, arginfo_kafka_metadata_get_orig_broker_name, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Metadata, getBrokers, arginfo_kafka_metadata_get_brokers, ZEND_ACC_PUBLIC)
    PHP_ME(RdKafka__Metadata, getTopics, arginfo_kafka_metadata_get_topics, ZEND_ACC_PUBLIC)
    PHP_FE_END
};
```

- RdKafka\Metadata
1. Metadata::getOrigBrokerId （）
> 获取当前metadata的broker ID。

```
PHP_METHOD(RdKafka__Metadata, getOrigBrokerId)
{
    ...
    RETURN_LONG(intern->metadata->orig_broker_id);
    ...
}
```

2. Metadata::getOrigBrokerName ()
> 获取当前metadata的broker name。

```
PHP_METHOD(RdKafka__Metadata, getOrigBrokerName)
{
    ...
    RDKAFKA_RETURN_STRING(intern->metadata->orig_broker_name);
    ...
}
```

3. Metadata::getBrokers()
> 获取broker 列表。

```
PHP_METHOD(RdKafka__Metadata, getBrokers)
{
    ...
    brokers_collection(return_value, getThis(), intern TSRMLS_CC);
    ...
}

static void brokers_collection(zval *return_value, zval *parent, object_intern *intern TSRMLS_DC) { /* {{{ */
    kafka_metadata_collection_init(return_value, parent, intern->metadata->brokers, intern->metadata->broker_cnt, sizeof(*intern->metadata->brokers), kafka_metadata_broker_ctor TSRMLS_CC);
}
```

4. Metadata::getTopics ()
> 获取topic 列表。

```
PHP_METHOD(RdKafka__Metadata, getTopics)
{
    ...
    topics_collection(return_value, getThis(), intern TSRMLS_CC);
    ...
}


static void topics_collection(zval *return_value, zval *parent, object_intern *intern TSRMLS_DC) { /* {{{ */
    kafka_metadata_collection_init(return_value, parent, intern->metadata->topics, intern->metadata->topic_cnt, sizeof(*intern->metadata->topics), kafka_metadata_topic_ctor TSRMLS_CC);
}
```

