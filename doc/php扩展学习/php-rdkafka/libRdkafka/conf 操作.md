> rdkafka_conf.c

```
// 配置时错误类型
typedef enum {
    RD_KAFKA_CONF_UNKNOWN = -2, /**< Unknown configuration name. */
    RD_KAFKA_CONF_INVALID = -1, /**< Invalid configuration value. */
    RD_KAFKA_CONF_OK = 0        /**< Configuration okay */
} rd_kafka_conf_res_t;
```


```
// 创建conf
rd_kafka_conf_t *rd_kafka_conf_new(void);
// 销毁conf
void rd_kafka_conf_destroy(rd_kafka_conf_t *conf);
// 复制conf
rd_kafka_conf_t *rd_kafka_conf_dup(const rd_kafka_conf_t *conf);
// 设置conf配置项：所有可以设置的配置项见 CONFIGURATION.md
rd_kafka_conf_res_t rd_kafka_conf_set(rd_kafka_conf_t *conf, const char *name, const char *value, char *errstr, size_t errstr_size);
```
1. 部分回调函数设置

```
// 1. producer 发送message后回调函数（成功/失败）：
void rd_kafka_conf_set_consume_cb (rd_kafka_conf_t *conf, void (*consume_cb) (rd_kafka_message_t * rkmessage,void *opaque));
// 推荐上面这个函数 或
void rd_kafka_conf_set_dr_cb(rd_kafka_conf_t *conf, void (*dr_cb) (rd_kafka_t *rk, void *payload, size_t len, rd_kafka_resp_err_t err, void *opaque, void *msg_opaque));

// 2. consumer接收message后回调，配合rd_kafka_consumer_poll()使用：
void rd_kafka_conf_set_consume_cb (rd_kafka_conf_t *conf, void (*consume_cb) (rd_kafka_message_t * rkmessage, void *opaque));

// 3. 重新负载均衡时候回调：
void rd_kafka_conf_set_rebalance_cb ( rd_kafka_conf_t *conf, void (*rebalance_cb) (rd_kafka_t *rk, rd_kafka_resp_err_t err, rd_kafka_topic_partition_list_t *partitions, void *opaque));

// 4. kafka内部发生严重错误回调：
void rd_kafka_conf_set_error_cb(rd_kafka_conf_t *conf, void  (*error_cb) (rd_kafka_t *rk, int err, const char *reason, void *opaque));

// 5. producer发送消息或consumer取消息时遇到broker返回限流时间时的回调，不管是返回一段时间还是返回0(限流取消)
void rd_kafka_conf_set_throttle_cb (rd_kafka_conf_t *conf, void (*throttle_cb) ( rd_kafka_t *rk, const char *broker_name, int32_t broker_id, int throttle_time_ms, void *opaque));

// 6. 设置日志输出的回调，默认是打印到stderr，内置的log_cb有rd_kafka_log_print(),
// rd_kafka_log_syslog()
void rd_kafka_conf_set_log_cb(rd_kafka_conf_t *conf, void (*log_cb) (const rd_kafka_t *rk, int level, const char *fac, const char *buf));

// 7. 统计时回调，由rd_kafka_poll()每隔statistics.interval.ms(需要单独设置)触发
void rd_kafka_conf_set_stats_cb(rd_kafka_conf_t *conf, int (*stats_cb) (rd_kafka_t *rk, char *json, size_t json_len, void *opaque));


```

