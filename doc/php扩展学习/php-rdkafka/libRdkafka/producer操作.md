### message flags

```
#define RD_KAFKA_MSG_F_FREE  0x1 /* Delegate freeing of payload to rdkafka. */
#define RD_KAFKA_MSG_F_COPY  0x2 /* rdkafka will make a copy of the payload. */
```

### 发送

```
// 发送一条
int rd_kafka_produce(rd_kafka_topic_t *rkt, int32_t partition, int msgflags, void *payload, size_t len,
 const void *key, size_t keylen, void *msg_opaque);

// 发送很多条
int rd_kafka_produce_batch(rd_kafka_topic_t *rkt, int32_t partition, int msgflags, rd_kafka_message_t *rkmessages, int message_cnt);
```

1. rd_kafka_produce
> 并没有直接发送，而是通过拦截器调用send事件，将消息放入 RecordAccumulator 暂存，等待发送

```
int rd_kafka_produce (rd_kafka_topic_t *rkt, int32_t partition, int msgflags, void *payload, size_t len, const void *key, size_t keylen, void *msg_opaque) 
{
        return rd_kafka_msg_new(rd_kafka_topic_a2i(rkt), partition, msgflags, payload, len, key, keylen, msg_opaque);
}

int rd_kafka_msg_new (rd_kafka_itopic_t *rkt, int32_t force_partition, int msgflags, char *payload, size_t len, const void *key, size_t keylen, void *msg_opaque) 
{
    ...
    /* Create message */
        rkm = rd_kafka_msg_new0(rkt, force_partition, msgflags, payload, len, key, keylen, msg_opaque, &err, &errnox, NULL, 0, rd_clock());
    ...
}
static rd_kafka_msg_t *rd_kafka_msg_new0 (rd_kafka_itopic_t *rkt, int32_t force_partition, int msgflags, char *payload, size_t len, const void *key, size_t keylen, void *msg_opaque, rd_kafka_resp_err_t *errp, int *errnop, rd_kafka_headers_t *hdrs, int64_t timestamp, rd_ts_t now) 
{
    ...
    rkm = rd_kafka_msg_new00(rkt, force_partition, msgflags|RD_KAFKA_MSG_F_ACCOUNT, payload, len, key, keylen, msg_opaque);
    
    // 调用拦截器on_send方法
    /* Call interceptor chain for on_send */
        rd_kafka_interceptors_on_send(rkt->rkt_rk, &rkm->rkm_rkmessage);

    ...
}
```
