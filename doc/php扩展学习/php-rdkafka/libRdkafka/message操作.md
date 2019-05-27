> rdkafka_msg.h 

```
typedef struct rd_kafka_message_s {
rd_kafka_resp_err_t err;   /* Non-zero for error signaling. */
rd_kafka_topic_t *rkt;     /* Topic */
int32_t partition;         /* Partition */
void   *payload;           /* Producer: original message payload.
                        * Consumer: Depends on the value of err :
                        *  err==0: Message payload.
                        *  err!=0: Error string */
size_t  len;               /* Depends on the value of err :
                        *  err==0: Message payload length
                        *  err!=0: Error string length */
void   *key;               /* Depends on the value of err :
                        *  err==0: Optional message key */
size_t  key_len;           /* Depends on the value of err :
                        *  err==0: Optional message key length*/
int64_t offset;            /* Consume:
                        *  Message offset (or offset for error
                        *   if err!=0 if applicable).
                        *  dr_msg_cb:
                        *   Message offset assigned by broker.
                        *   If produce.offset.report is set then
                        *   each message will have this field set,
                        *   otherwise only the last message in
                        *   each produced internal batch will
                        *   have this field set, otherwise 0. */
void  *_private;           /* Consume:
                        *   rdkafka private pointer: DO NOT MODIFY
                        *   dr_msg_cb:
                        *    msg_opaque from produce() call */
} rd_kafka_message_t;

```


```
// 1. 新建message
int rd_kafka_msg_new (rd_kafka_itopic_t *rkt, int32_t force_partition, int msgflags, char *payload, size_t len, const void *keydata, size_t keylen, void *msg_opaque);

// 2. 销毁消息
void rd_kafka_msg_destroy (rd_kafka_t *rk, rd_kafka_msg_t *rkm);

// 3. messgae 的错误信息
static const char * rd_kafka_message_errstr(const rd_kafka_message_t *rkmessage);

```