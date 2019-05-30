- libRdKafka是什么？
> 1. c语言实现的kafka客户端。对rdkafka扩展提供api接口。他是基于现代硬件设计的多线程库，并且试图保持最少的内存拷贝。
> 2. 高性能特性：
>    1. 高吞吐：  
        > 消息的批量处理，等待本地队列累计一定数量的消息，然后用一个大的消息集或批量发送到对端。 
        通过这种方式补偿通讯开销和消除往返时延(RTT)的不利影响。
>    2. 低延迟：
        > 可以根据实际情况调整queue.buffering.max.ms参数，适当降低延迟。
>    3. 压缩：
        > 生产者消息压缩通过配置“compression.codec”属性生效。压缩通过本地队列批量处理消息实现，批量越大越可能获得更高的压缩率。 
>    4. 消息可靠性：
        > 多副本复制原理和副本同步队列方式保证数据可靠性。
```
// 高吞吐配置项
batch.num.messages=1000 // 批量发送消息数
queue.buffering.max.ms=1000 // 消息队列等待时间

// 消息可靠性配置项
request.required.acks // 设置为等待来自broker的消息提交确认：0为生产者producer不等待来自broker同步完成的确认继续发送下一条（批）消息。这种方式是最低的延迟但是最弱的耐久性保证；1为producer在leader已成功收到的数据并得到确认后发送下一条message，这种方式保证了耐久性。-1为producer在follower副本确认接收到数据后才算一次发送完成。 此选项提供最好的耐久性，我们保证没有信息将丢失，只要至少一个同步副本保持存活。

message.send.max.retries // 消息失败重新发送次数。
```

- libRdkafka的元素
1. 顶层rdkafka对象rd_kafka_t,基础容器用于全局配置和共享状态。 

```
rd_kafka_conf_t *rd_kafka_conf_new(void);

rd_kafka_t *rd_kafka_new(rd_kafka_type_t type, rd_kafka_conf_t *conf, char *errstr, size_t errstr_size);
);

struct rd_kafka_s {
    rd_kafka_q_t *rk_rep;   /* kafka -> application reply queue */
	rd_kafka_q_t *rk_ops;   /* any -> rdkafka main thread ops */

	TAILQ_HEAD(, rd_kafka_broker_s) rk_brokers;
	
	...
	TAILQ_HEAD(, rd_kafka_itopic_s)  rk_topics;
    rd_kafka_conf_t  rk_conf;
	rd_kafka_type_t  rk_type;
    struct rd_kafka_metadata *rk_full_metadata; 
    ...
}

// KAFKA TYPE 
typedef enum rd_kafka_type_t {
	RD_KAFKA_PRODUCER, /**< Producer client */
	RD_KAFKA_CONSUMER  /**< Consumer client */
} rd_kafka_type_t;

// KAFKA CONF by calling rd_kafka_conf_set().
struct rd_kafka_conf_s {
    ...
	int     metadata_request_timeout_ms;
	int     metadata_refresh_interval_ms;
	int     metadata_refresh_fast_cnt;
	int     metadata_refresh_fast_interval_ms;
	...
}

```

2. 实例化rd_kafka_topic_t对象用于生产和消费。

```
rd_kafka_topic_conf_t *rd_kafka_topic_conf_new(void);

rd_kafka_topic_t *rd_kafka_topic_new(rd_kafka_t *rk, const char *topic, rd_kafka_topic_conf_t *conf);


// kafka topic
struct rd_kafka_itopic_s {
    ...
	rd_refcnt_t        rkt_refcnt;
	rwlock_t           rkt_lock;
	rd_kafkap_str_t   *rkt_topic;

	rd_kafka_t       *rkt_rk;
    
    ...
    
	rd_kafka_topic_conf_t rkt_conf;
}

// kafka topic conf  By calling rd_kafka_topic_conf_set()
typedef struct rd_kafka_topic_conf_s rd_kafka_topic_conf_t;
struct rd_kafka_topic_conf_s {
    ...
    
    int     required_acks;
	int32_t request_timeout_ms;
	int     message_timeout_ms;
	...
}

```

3. libRdkafka 线程和回调
> 1. libRdkafka 本身支持多线程。API 是完全线程安全。在任何时间和其任何线程中，应用程序都可以调用任何 API 函数。
> 2. kafka响应事件：rd_kafka_poll() ,应用程序会定期调用这个函数。

```
    int rd_kafka_poll(rd_kafka_t *rk, int timeout_ms);
    rd_kafka_message_t *rd_kafka_consumer_poll (rd_kafka_t *rk, int timeout_ms);
```
> 3. api可以配置消息发送报告回调，错误回调函数，日志回调函数，分区回调函数...

4. metadata 
> kafka 元数据，保存broker信息和topic信息。
```
typedef struct rd_kafka_metadata {
int         broker_cnt;     /**< Number of brokers in \p brokers */
struct rd_kafka_metadata_broker *brokers;  /**< Brokers */

int         topic_cnt;      /**< Number of topics in \p topics */
struct rd_kafka_metadata_topic *topics;    /**< Topics */

int32_t     orig_broker_id;   /**< Broker originating this metadata */
char       *orig_broker_name; /**< Name of originating broker */
} rd_kafka_metadata_t;
```


4. brokers
> libRdkafka 只需要初始化一个broker列表到rd_kafka_metadata中来调用broker的引导。  
在“metadata.broker.list”属性中或调用rd_kafka_brokers_add()添加的 broker 引导。

```
int rd_kafka_brokers_add(rd_kafka_t *rk, const char *brokerlist);

typedef struct rd_kafka_metadata_broker {
        int32_t     id;             /**< Broker Id */
        char       *host;           /**< Broker hostname */
        int         port;           /**< Broker listening port */
} rd_kafka_metadata_broker_t;

```


- Producer API
> 单条发消息rd_kafka_produce()和多条发消息rd_kafka_produce_batch()

```
int rd_kafka_produce (rd_kafka_topic_t *rkt, int32_t partition, int msgflags, void *payload, size_t len, const void *key, size_t keylen, void *msg_opaque) 
{
return rd_kafka_msg_new(rd_kafka_topic_a2i(rkt), partition, msgflags, payload, len, key, keylen, msg_opaque);
}

int rd_kafka_produce_batch (rd_kafka_topic_t *app_rkt, int32_t partition, int msgflags, rd_kafka_message_t *rkmessages, int message_cnt) 
{
    ...
     for (i = 0 ; i < message_cnt ; i++) {
         ...
            rkm = rd_kafka_msg_new0(rkt, (msgflags & RD_KAFKA_MSG_F_PARTITION) ? rkmessages[i].partition : partition, msgflags,  rkmessages[i].len, rkmessages[i].key, rkmessages[i].key_len, rkmessages[i]._private, &rkmessages[i].err, NULL, NULL, utc_now, now);
		...			
     }
    ...
}


// 参数说明：
1. rkt - 生产的topic，之前通过rd_kafka_topic_new()生成
2. partition - 生产的 partition。如果设置为RD_KAFKA_PARTITION_UA（未赋值的），需要通过配置分区函数去选择一个确定 partition。
3. msgflags - 0 或下面的值：
    RD_KAFKA_MSG_F_COPY - librdkafka 将立即从 payload 做一份拷贝。如果 payload 是不稳定存储，如栈，需要使用这个参数。
    RD_KAFKA_MSG_F_FREE - 当 payload 使用完后，让 librdkafka 使用free(3)释放。
    这两个标志互斥，如果都不设置，payload 既不会被拷贝也不会被 librdkafka 释放。
    如果RD_KAFKA_MSG_F_COPY标志不设置，就会有数据拷贝，librdkafka 将占用 payload 指针直到消息被发送或失败。 
    librdkafka 处理完消息后，会调用发送报告回调函数，让应用程序重新获取 payload 的所有权。 
    如果设置了RD_KAFKA_MSG_F_FREE，应用程序就不要在发送报告回调函数中释放 payload。
4. payload,len - 消息 payload
5. key,keylen - 可选的消息键，用于分区。将会用于 topic 分区回调函数，如果有，会附加到消息中发送给 broker。
6. msg_opaque - 可选的，应用程序为每个消息提供的无类型指针，提供给消息发送回调函数，用于应用程序引用。

```
1. rd_kafka_produce 是非阻塞的，将消息塞入一个内部队列后立即返回，队列的消息数大于batch.num.messages才会触发send，如果队列中的消息数超过queue.buffering.max.messages属性配置的值，rd_kafka_produce()通过返回 -1，并在ENOBUFS中设置错误码来反馈错误。




- Consumer API
> 低级simpple consumer和高级High-level consumer两套API。区别在于高级自动做了负载均衡。

```
// 高级API
msg = rd_kafka_consumer_poll();
// 低级API
// 一次收一条
msg = rd_kafka_consume();
// 一次收多条
msg = rd_kafka_consume_batch();
// 使用回调处理收到的msg，速度最快了
rd_kafka_consume_callback();


rd_kafka_message_t *rd_kafka_consume(rd_kafka_topic_t *rkt, int32_t partition, int timeout_ms);

ssize_t rd_kafka_consume_batch (rd_kafka_topic_t *app_rkt, int32_t partition, int timeout_ms, rd_kafka_message_t **rkmessages, size_t rkmessages_size) 
{
    ...
    
    s_rktp = rd_kafka_toppar_get(rkt, partition, 0/*no ua on miss*/);
    if (unlikely(!s_rktp))
    s_rktp = rd_kafka_toppar_desired_get(rkt, partition);
    rd_kafka_topic_rdunlock(rkt);
    ...
    cnt = rd_kafka_q_serve_rkmessages(rktp->rktp_fetchq, timeout_ms,
    rkmessages, rkmessages_size);
    
   ...
}

int rd_kafka_consume_callback (rd_kafka_topic_t *app_rkt, int32_t partition, int timeout_ms, void (*consume_cb) (rd_kafka_message_t *rkmessage, void *opaque), void *opaque) 
{
    ...
    
    rd_kafka_topic_rdlock(rkt);
    s_rktp = rd_kafka_toppar_get(rkt, partition, 0/*no ua on miss*/);
    if (unlikely(!s_rktp))
    s_rktp = rd_kafka_toppar_desired_get(rkt, partition);
    rd_kafka_topic_rdunlock(rkt);
    
    ...
    
    r = rd_kafka_consume_callback0(rktp->rktp_fetchq, timeout_ms, rkt->rkt_conf.consume_callback_max_msgs, consume_cb, opaque);
    
    ...
}

rd_kafka_message_t *rd_kafka_consumer_poll (rd_kafka_t *rk, int timeout_ms) 
{
rd_kafka_cgrp_t *rkcg;
if (unlikely(!(rkcg = rd_kafka_cgrp_get(rk)))) 
{
rd_kafka_message_t *rkmessage = rd_kafka_message_new();
rkmessage->err = RD_KAFKA_RESP_ERR__UNKNOWN_GROUP;
return rkmessage;
}

return rd_kafka_consume0(rk, rkcg->rkcg_q, timeout_ms);
}

```
1. 高级consumer API不用关心topic的condition，offset。自动由zookeeper自行管理，可能自行控制能力差。
2. 低级consumer API使用复杂，需要自行控制topic的condition和offset。需要自定义复杂均衡处理。
3. 大部分场景使用高级API就可以满足，除非需要自定义控制负载和消息偏移。



