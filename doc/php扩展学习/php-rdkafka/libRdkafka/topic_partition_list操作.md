> rdkafka.h  rdkafka_partition.h
1. topic_partition_list 操作

```
// 表示一个topic+partition
typedef struct rd_kafka_topic_partition_s {
    char        *topic;             /* Topic name */
    int32_t      partition;         /* Partition */
    int64_t      offset;            /* Offset */
    void        *metadata;          /* Metadata */
    size_t       metadata_size;     /* Metadata size */
    void        *opaque;            /* Application opaque */
    rd_kafka_resp_err_t err;        /* Error code, depending on use. */
    void       *_private;           /* INTERNAL USE ONLY,
                                         *   INITIALIZE TO ZERO, DO NOT TOUCH */
} rd_kafka_topic_partition_t;
// 存放的list，一般是操作这个结构
typedef struct rd_kafka_topic_partition_list_s {
    int cnt;               /* Current number of elements */
    int size;              /* Current allocated size */
    rd_kafka_topic_partition_t *elems; /* Element array[] */
} rd_kafka_topic_partition_list_t;
```

```
//1. 新建，销毁list
rd_kafka_topic_partition_list_t *rd_kafka_topic_partition_list_new (int size);

void rd_kafka_topic_partition_list_destroy (rd_kafka_topic_partition_list_t *rkparlist);

// 2. 添加元素
// 添加1个元素，返回的element可用于填写其他field
rd_kafka_topic_partition_t * rd_kafka_topic_partition_list_add ( rd_kafka_topic_partition_list_t *rktparlist, const char *topic, int32_t partition);
// 添加多个元素
void rd_kafka_topic_partition_list_add_range ( rd_kafka_topic_partition_list_t *rktparlist, const char *topic, int32_t start, int32_t stop);

// 3. 删除元素
// 根据topic+partition删除
int rd_kafka_topic_partition_list_del ( rd_kafka_topic_partition_list_t *rktparlist, const char *topic, int32_t partition);
// 根据index删除
int rd_kafka_topic_partition_list_del_by_idx ( rd_kafka_topic_partition_list_t *rktparlist, int idx);

// 4. 查找元素
rd_kafka_topic_partition_t * rd_kafka_topic_partition_list_find ( rd_kafka_topic_partition_list_t *rktparlist, const char *topic, int32_t partition);

```
