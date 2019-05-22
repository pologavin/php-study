1. 启动zookeeper

```
./zkServer.sh start
```

2. 查看zookeeper是否启动

```
ps -ef |grep java
```
3. 查看zookeeper的状态

```
./zkServer.sh status
```

4. 关闭zookeeper

```
./zkServer.sh stop
```

5. 启动kafka

```
./kafka-server-start.sh -daemon ../config/server.properties &
```

6. 查看kafka是否启动

```
 ps -ef |grep kafka
```

7. 关闭kafka

```
./kafka-server-stop.sh
```

8. 创建topic

```
./kafka-topics.sh --create --zookeeper 172.16.218.201:2181,172.16.218.202:2181,172.16.218.203:2181 --replication-factor 3 --partitions 1 --topic szy
```

9. 查看所有topic

```
./kafka-topics.sh --list --zookeeper 172.16.218.201:2181,172.16.218.202:2181,172.16.218.203:2181
```

10. 查看某个topic具体信息

```
./kafka-topics.sh --describe --zookeeper 172.16.218.201:2181,172.16.218.202:2181,172.16.218.203:2181 --topic szy
```

11. 删除topic (可直接删除的前提：delete.topic.enable=true)

```
./kafka-topics.sh --delete --zookeeper 172.16.218.201:2181,172.16.218.202:2181,172.16.218.203:2181 --topic test 
命令无法删除，topic被标记为删除时候，用下面命令删除
# cd /usr/local/zookeeper-node3/bin/
# ./zkCli.sh
ls /brokers/topics
rmr /brokers/topics/【topic name】
quit
```

12. 生产消息

```
./kafka-console-producer.sh --broker-list 172.16.218.201:19092,172.16.218.202:19092,172.16.218.203:19092 --topic szy

```

13. 消费消息

```
./kafka-console-consumer.sh --zookeeper 172.16.218.201:2181,172.16.218.202:2181,172.16.218.203:2181 --topic szy --from-beginning
```
