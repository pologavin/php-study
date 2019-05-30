> rdkafka.c    
> 有低级和高级两套API，高级API自动支持负载均衡。低级自定义实现负载均衡。


1. message 的offset可以为绝对数或者以下逻辑值

```
RD_KAFKA_OFFSET_BEGINNING
RD_KAFKA_OFFSET_END
RD_KAFKA_OFFSET_STORED
RD_KAFKA_OFFSET_TAIL
```

- 低级API

```
// 1. 开始接收message
int rd_kafka_consume_start(rd_kafka_topic_t *rkt, int32_t partition, int64_t offset);


// 2. 停止接收
int rd_kafka_consume_stop(rd_kafka_topic_t *rkt, int32_t partition);


// 3. 查找现在的offset
rd_kafka_resp_err_t rd_kafka_seek (rd_kafka_topic_t *rkt, int32_t partition, int64_t offset, int timeout_ms);


// 4. 接收一条message
rd_kafka_message_t *rd_kafka_consume(rd_kafka_topic_t *rkt, int32_t partition, int timeout_ms);


// 5. 接收多条message
ssize_t rd_kafka_consume_batch(rd_kafka_topic_t *rkt, int32_t partition, int timeout_ms, rd_kafka_message_t **rkmessages, size_t rkmessages_size);


// 6. 使用回调处理收到的message，速度最快
int rd_kafka_consume_callback(rd_kafka_topic_t *rkt, int32_t partition, int timeout_ms, void (*consume_cb) (rd_kafka_message_t *rkmessage, void *opaque), void *opaque);


// 7. 手动commit
rd_kafka_resp_err_t rd_kafka_offset_store(rd_kafka_topic_t *rkt,
int32_t partition, int64_t offset);
```


- 高级API

```
// 1. 订阅topic
rd_kafka_resp_err_t rd_kafka_subscribe (rd_kafka_t *rk, const rd_kafka_topic_partition_list_t *topics);


// 2. 取消订阅
rd_kafka_resp_err_t rd_kafka_unsubscribe (rd_kafka_t *rk);


// 3. 获取当前的订阅
rd_kafka_resp_err_t rd_kafka_subscription (rd_kafka_t *rk, rd_kafka_topic_partition_list_t **topics);


// 4. 消费message
rd_kafka_message_t *rd_kafka_consumer_poll (rd_kafka_t *rk, int timeout_ms);


// 5. 关闭consumer
rd_kafka_resp_err_t rd_kafka_consumer_close (rd_kafka_t *rk);


// 6. 分派partition,跟rd_kafka_subscribe()的区别就是assign必须有partition才生效，
// 当你知道partition的时候，最好用这个，因为kafka不用再去找了，速度最快
rd_kafka_resp_err_t rd_kafka_assign (rd_kafka_t *rk, const rd_kafka_topic_partition_list_t *partitions);


// 7. 获取当前分派的partition
rd_kafka_resp_err_t rd_kafka_assignment (rd_kafka_t *rk, rd_kafka_topic_partition_list_t **partitions);


// 8. 手动commit，可以同步可以异步，异步的时候要设置rd_kafka_conf_set_offset_commit_cb()
rd_kafka_resp_err_t rd_kafka_commit (rd_kafka_t *rk, const rd_kafka_topic_partition_list_t *offsets, int async);


// 9. 手动commit
rd_kafka_resp_err_t rd_kafka_commit_message (rd_kafka_t *rk, const rd_kafka_message_t *rkmessage, int async);


// 10. 获取当前commit的offset
rd_kafka_resp_err_t rd_kafka_committed (rd_kafka_t *rk, rd_kafka_topic_partition_list_t *partitions, int timeout_ms);


// 11. 获取当前topic+partition的offset
rd_kafka_resp_err_t rd_kafka_position (rd_kafka_t *rk, rd_kafka_topic_partition_list_t *partitions);

```

1. 要调用rd_kafka_consumer_poll()，还需要一个高级API:

```
// 将接收到 queue 从rd_kafka_poll()重定向到rd_kafka_poll_set_consumer()
rd_kafka_resp_err_t rd_kafka_poll_set_consumer (rd_kafka_t *rk);
```
