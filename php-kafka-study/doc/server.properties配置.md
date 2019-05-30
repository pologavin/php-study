
```
# Licensed to the Apache Software Foundation (ASF) under one or more
# contributor license agreements.  See the NOTICE file distributed with
# this work for additional information regarding copyright ownership.
# The ASF licenses this file to You under the Apache License, Version 2.0
# (the "License"); you may not use this file except in compliance with
# the License.  You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
# see kafka.server.KafkaConfig for additional details and defaults

############################# Server Basics #############################

##################################################################################
#  broker就是一个kafka的部署实例，在一个kafka集群中，每一台kafka都要有一个broker.id
#  并且，该id唯一，且必须为整数
##################################################################################
broker.id=10

############################# Socket Server Settings #############################

# The address the socket server listens on. It will get the value returned from 
# java.net.InetAddress.getCanonicalHostName() if not configured.
#   FORMAT:
#     listeners = security_protocol://host_name:port
#   EXAMPLE:
#     listeners = PLAINTEXT://your.host.name:9092
#listeners=PLAINTEXT://:9092

# Hostname and port the broker will advertise to producers and consumers. If not set, 
# it uses the value for "listeners" if configured.  Otherwise, it will use the value
# returned from java.net.InetAddress.getCanonicalHostName().
#advertised.listeners=PLAINTEXT://your.host.name:9092

##################################################################################
#The number of threads handling network requests
# 默认处理网络请求的线程个数 3个
##################################################################################
num.network.threads=3
##################################################################################
# The number of threads doing disk I/O
# 执行磁盘IO操作的默认线程个数 8
##################################################################################
num.io.threads=8

##################################################################################
# The send buffer (SO_SNDBUF) used by the socket server
# socket服务使用的进行发送数据的缓冲区大小，默认100kb
##################################################################################
socket.send.buffer.bytes=102400

##################################################################################
# The receive buffer (SO_SNDBUF) used by the socket server
# socket服务使用的进行接受数据的缓冲区大小，默认100kb
##################################################################################
socket.receive.buffer.bytes=102400

##################################################################################
# The maximum size of a request that the socket server will accept (protection against OOM)
# socket服务所能够接受的最大的请求量，防止出现OOM(Out of memory)内存溢出，默认值为：100m
# （应该是socker server所能接受的一个请求的最大大小，默认为100M）
##################################################################################
socket.request.max.bytes=104857600

############################# Log Basics （数据相关部分，kafka的数据称为log）#############################

##################################################################################
# A comma seperated list of directories under which to store log files
# 一个用逗号分隔的目录列表，用于存储kafka接受到的数据
##################################################################################
log.dirs=/home/uplooking/data/kafka

##################################################################################
# The default number of log partitions per topic. More partitions allow greater
# parallelism for consumption, but this will also result in more files across
# the brokers.
# 每一个topic所对应的log的partition分区数目，默认1个。更多的partition数目会提高消费
# 并行度，但是也会导致在kafka集群中有更多的文件进行传输
# （partition就是分布式存储，相当于是把一份数据分开几份来进行存储，即划分块、划分分区的意思）
##################################################################################
num.partitions=1

##################################################################################
# The number of threads per data directory to be used for log recovery at startup and flushing at shutdown.
# This value is recommended to be increased for installations with data dirs located in RAID array.
# 每一个数据目录用于在启动kafka时恢复数据和在关闭时刷新数据的线程个数。如果kafka数据存储在磁盘阵列中
# 建议此值可以调整更大。
##################################################################################
num.recovery.threads.per.data.dir=1

############################# Log Flush Policy （数据刷新策略）#############################

# Messages are immediately written to the filesystem but by default we only fsync() to sync
# the OS cache lazily. The following configurations control the flush of data to disk.
# There are a few important trade-offs（平衡） here:
#    1. Durability 持久性: Unflushed data may be lost if you are not using replication.
#    2. Latency 延时性: Very large flush intervals may lead to latency spikes when the flush does occur as there will be a lot of data to flush.
#    3. Throughput 吞吐量: The flush is generally the most expensive operation, and a small flush interval may lead to exceessive seeks.
# The settings below allow one to configure the flush policy to flush data after a period of time or
# every N messages (or both). This can be done globally and overridden on a per-topic basis.
# kafka中只有基于消息条数和时间间隔数来制定数据刷新策略，而没有大小的选项，这两个选项可以选择配置一个
# 当然也可以两个都配置，默认情况下两个都配置，配置如下。

# The number of messages to accept before forcing a flush of data to disk
# 消息刷新到磁盘中的消息条数阈值
#log.flush.interval.messages=10000

# The maximum amount of time a message can sit in a log before we force a flush
# 消息刷新到磁盘生成一个log数据文件的时间间隔
#log.flush.interval.ms=1000

############################# Log Retention Policy（数据保留策略） #############################

# The following configurations control the disposal（清理） of log segments（分片）. The policy can
# be set to delete segments after a period of time, or after a given size has accumulated（累积）.
# A segment will be deleted whenever（无论什么时间） *either* of these criteria（标准） are met. Deletion always happens
# from the end of the log.
# 下面的配置用于控制数据片段的清理，只要满足其中一个策略（基于时间或基于大小），分片就会被删除

# The minimum age of a log file to be eligible for deletion
# 基于时间的策略，删除日志数据的时间，默认保存7天
log.retention.hours=168

# A size-based retention policy for logs. Segments are pruned from the log as long as the remaining
# segments don't drop below log.retention.bytes. 1G
# 基于大小的策略，1G
#log.retention.bytes=1073741824

# The maximum size of a log segment file. When this size is reached a new log segment will be created.
# 数据分片策略
log.segment.bytes=1073741824

# The interval at which log segments are checked to see if they can be deleted according
# to the retention policies 5分钟
# 每隔多长时间检测数据是否达到删除条件
log.retention.check.interval.ms=300000

############################# Zookeeper #############################

# Zookeeper connection string (see zookeeper docs for details).
# This is a comma separated host:port pairs, each corresponding to a zk
# server. e.g. "127.0.0.1:3000,127.0.0.1:3001,127.0.0.1:3002".
# You can also append an optional chroot string to the urls to specify the
# root directory for all kafka znodes.
zookeeper.connect=uplooking01:2181,uplooking02:2181,uplooking03:2181

# Timeout in ms for connecting to zookeeper
zookeeper.connection.timeout.ms=6000

```
