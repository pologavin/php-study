> rdkafka.c


```
// 1. kafka的创建
rd_kafka_t *rd_kafka_new (rd_kafka_type_t type, rd_kafka_conf_t *app_conf, char *errstr, size_t errstr_size);

// kafka类型
typedef enum rd_kafka_type_t {
	RD_KAFKA_PRODUCER, /**< Producer client */
	RD_KAFKA_CONSUMER  /**< Consumer client */
} rd_kafka_type_t;


// 2. kafka的销毁
void rd_kafka_destroy(rd_kafka_t *rk);


// 3. kafka事件捕捉：
// delivery report callbacks  (if dr_cb/dr_msg_cb is configured) [producer]
// error callbacks (rd_kafka_conf_set_error_cb()) [all]
// stats callbacks (rd_kafka_conf_set_stats_cb()) [all]
// throttle callbacks (rd_kafka_conf_set_throttle_cb()) [all]
int rd_kafka_poll(rd_kafka_t *rk, int timeout_ms);


// 4. 取消掉该次事件，只能在各种callback中使用
void rd_kafka_yield (rd_kafka_t *rk);


// 5. 暂停某些partitions发送或接收消息
rd_kafka_resp_err_t rd_kafka_pause_partitions (rd_kafka_t *rk, rd_kafka_topic_partition_list_t *partitions);


// 6. 恢复暂停的partitions的工作
rd_kafka_resp_err_t rd_kafka_resume_partitions (rd_kafka_t *rk, rd_kafka_topic_partition_list_t *partitions);
                                
                                
                                
// 7. 获取当前partition的高低水位，阻塞
rd_kafka_resp_err_t rd_kafka_query_watermark_offsets (rd_kafka_t *rk, const char *topic, int32_t partition, int64_t *low, int64_t *high, int timeout_ms);
                                
                                
// 获取当前partition的高低水位，非阻塞
rd_kafka_resp_err_t rd_kafka_get_watermark_offsets (rd_kafka_t *rk, const char *topic, int32_t partition, int64_t *low, int64_t *high);


```
