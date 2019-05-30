> 将来自几个不同的topic+partition的message汇总到一个queue中交给应用去处理.   
> rdkafka_queue.c

```
// 1. kafka_queue 创建
rd_kafka_queue_t *rd_kafka_queue_new(rd_kafka_t *rk);


// 2. kafka_queue 销毁
void rd_kafka_queue_destroy(rd_kafka_queue_t *rkqu);

// 3. 开始kafka_queue。 重新路由的操作，不能对相同的topic+partition调用多次
int rd_kafka_consume_start_queue(rd_kafka_topic_t *rkt, int32_t partition, int64_t offset, rd_kafka_queue_t *rkqu);


// 4. 接收单条message
rd_kafka_message_t *rd_kafka_consume_queue(rd_kafka_queue_t *rkqu, int timeout_ms);

// 5. 接收多条message
ssize_t rd_kafka_consume_batch_queue(rd_kafka_queue_t *rkqu, int timeout_ms, rd_kafka_message_t **rkmessages, size_t rkmessages_size);


// 6. 使用回调去处理接收到的消息，处理速度最快
int rd_kafka_consume_callback_queue(rd_kafka_queue_t *rkqu, int timeout_ms, void (*consume_cb) (rd_kafka_message_t *rkmessage, void *opaque), void *opaque);


```
