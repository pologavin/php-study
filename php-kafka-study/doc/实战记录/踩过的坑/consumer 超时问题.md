### kafka consumer 高级api RdKafka\KafkaConsumer::consume 返回超时错误码RD_KAFKA_RESP_ERR__TIMED_OUT（-185）

- 现象
> 使用常驻进程consumer，频繁返回超时错误码RD_KAFKA_RESP_ERR__TIMED_OUT。

- 排查
1. 查看rdkafka KafkaConsumer::consume源码,发现问题

```
rkmessage = rd_kafka_consumer_poll(intern->rk, timeout_ms);

    // 问题在这：返回null，默认返回错误码为超时。
    if (!rkmessage) {
        rkmessage_tmp.err = RD_KAFKA_RESP_ERR__TIMED_OUT;
        rkmessage = &rkmessage_tmp;
    }
```
2. 查看librdkafka rd_kafka_consume0，返回问题

```
rd_ts_t abs_timeout = rd_timeout_init(timeout_ms);

 while ((rko = rd_kafka_q_pop(rkq, rd_timeout_remains(abs_timeout), 0))) {
               ...
        }
// 问题在这：rd_kafka_q_pop返回null，即设置错误码为超时，且返回null
if (!rko) {
		/* Timeout reached with no op returned. */
		rd_kafka_set_last_error(RD_KAFKA_RESP_ERR__TIMED_OUT,
					ETIMEDOUT);
		return NULL;
	}
```

- 结论
1. 返回超时错误可能是没有消息返回null。这个是kafka consumer pull消息拉取不到返回空数据。
而librdkafka返回空会设置错误码为超时。

 - 解决方案
 1. 处理超时RD_KAFKA_RESP_ERR__TIMED_OUT和处理无消息RD_KAFKA_RESP_ERR__PARTITION_EOF同样处理。
 

- 备注
> 使用低级api RdKafka\ConsumerTopic::consume,同样情况是返回空值。

```
  message = rd_kafka_consume(intern->rkt, partition, timeout_ms);

    if (!message) {
        err = rd_kafka_errno2err(errno);
        if (err == RD_KAFKA_RESP_ERR__TIMED_OUT) {
            return;
        }
        zend_throw_exception(ce_kafka_exception, rd_kafka_err2str(err), err TSRMLS_CC);
        return;
    }
```


