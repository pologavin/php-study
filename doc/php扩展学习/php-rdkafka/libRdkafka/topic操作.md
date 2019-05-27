> rdkafka_conf.c   rdkafka_topic.c

1. topic_conf 的操作

```
// 1. topic_conf 创建
rd_kafka_topic_conf_t *rd_kafka_topic_conf_new (void) 
{
rd_kafka_topic_conf_t *tconf = rd_calloc(1, sizeof(*tconf));
rd_kafka_defaultconf_set(_RK_TOPIC, tconf);
rd_kafka_anyconf_clear_all_is_modified(tconf);
return tconf;
}

// 2. 设置topic_conf 配置项
rd_kafka_conf_res_t rd_kafka_topic_conf_set (rd_kafka_topic_conf_t *conf, const char *name, const char *value, char *errstr, size_t errstr_size)
{
if (!strncmp(name, "topic.", strlen("topic.")))
name += strlen("topic.");

return rd_kafka_anyconf_set(_RK_TOPIC, conf, name, value, errstr, errstr_size);
}

// 3. 复制topic_conf
rd_kafka_topic_conf_t *rd_kafka_topic_conf_dup (const rd_kafka_topic_conf_t *conf) 
{
rd_kafka_topic_conf_t *new = rd_kafka_topic_conf_new();

rd_kafka_anyconf_copy(_RK_TOPIC, new, conf, 0, NULL);

return new;
}

// 4. 销毁topic_conf
void rd_kafka_topic_conf_destroy (rd_kafka_topic_conf_t *topic_conf) 
{
	rd_kafka_anyconf_destroy(_RK_TOPIC, topic_conf);
	rd_free(topic_conf);
}

```

2. topic 的操作

```
// 1. topic 的创建
rd_kafka_topic_t *rd_kafka_topic_new (rd_kafka_t *rk, const char *topic, rd_kafka_topic_conf_t *conf)
{
shptr_rd_kafka_itopic_t *s_rkt;
rd_kafka_itopic_t *rkt;
rd_kafka_topic_t *app_rkt;
int existing;

s_rkt = rd_kafka_topic_new0(rk, topic, conf, &existing, 1/*lock*/);
if (!s_rkt)
return NULL;

rkt = rd_kafka_topic_s2i(s_rkt);

/* Save a shared pointer to be used in callbacks. */
app_rkt = rd_kafka_topic_keep_app(rkt);

/* Query for the topic leader (async) */
if (!existing)
rd_kafka_topic_leader_query(rk, rkt);

/* Drop our reference since there is already/now a rkt_app_rkt */
rd_kafka_topic_destroy0(s_rkt);

return app_rkt;
}

// 2. topic 的销毁
void rd_kafka_topic_destroy (rd_kafka_topic_t *app_rkt) 
{
	rd_kafka_topic_destroy_app(app_rkt);
}
```
