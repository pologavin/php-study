php kafka study

- kafka是什么
1. 分布式的**发布/订阅消息**系统；
2. 最初由LinkedIn开发发布，使用scala语言编写，与2010年12月份开源，成为Apache的顶级项目。
3. 三大特性：
> 1. **高吞吐量**: 可以满足每秒百万级别消息的生产和消费——生产消费。即使在非常廉价的商用机器上也能做到单机支持每秒100K条以上消息的传输。
> 2. **持久性**：有一套完善的消息存储机制，确保数据的高效安全的持久化——中间存储。以时间复杂度为O(1)的方式提供消息持久化能力，即使对TB级以上数据也能保证常数时间复杂度的访问性能。
> 3. **分布式**：基于分布式的扩展和容错机制；Kafka的数据都会复制到几台服务器上。当某一台故障失效时，生产者和消费者转而使用其它的机器——整体健壮性。

4. 相关名词：

```
1. 消息 Message
   网络中的两台计算机或者两个通讯设备之间传递的数据。例如说：文本、音乐、视频等内容。
2. 队列 Queue
   一种特殊的线性表（数据元素首尾相接），特殊之处在于只允许在首部删除元素和在尾部追加元素。入队、出队。
3. 消息队列 MQ
   消息+队列，保存消息的队列。消息的传输过程中的容器；主要提供生产、消费接口供外部调用做数据的存储和获取。
4. MQ分类：
    MQ主要分为两类：点对点(p2p)、发布订阅(Pub/Sub)
共同点：
   消息生产者生产消息发送到queue中，然后消息消费者从queue中读取并且消费消息。
不同点：
   p2p模型包括：消息队列(Queue)、发送者(Sender)、接收者(Receiver)
    一个生产者生产的消息只有一个消费者(Consumer)(即一旦被消费，消息就不在消息队列中)。比如说打电话。

   Pub/Sub包含：消息队列(Queue)、主题(Topic)、发布者(Publisher)、订阅者(Subscriber)
   每个消息可以有多个消费者，彼此互不影响。比如我发布一个微博：关注我的人都能够看到。
   那么在大数据领域呢，为了满足日益增长的数据量，也有一款可以满足百万级别消息的生成和消费，分布式、持久稳定的产品——Kafka。

```

- kafka 是怎么运行的
1. kafka的架构
![image](http://www.gavin.xin/wp-content/uploads/2017/03/image2016-11-10-10_33_25.png)
这个是典型的分布式架构，由zookeeper管理kafka集群。producer、broker（kafka）和consumer都可以有多个。Producer，consumer实现Kafka注册的接口，数据从producer发送到broker，broker承担一个中间缓存和分发的作用。broker分发注册到系统中的consumer。broker的作用类似于缓存，即活跃的数据和离线处理系统之间的缓存。客户端和服务器端的通信，是基于简单，高性能，且与编程语言无关的TCP协议。
2. 家族成员：

```
1. Broker
    kafka集群中包含一个或者多个服务器，即每个服务器节点就被称为broker。他是消息代理者，存放消息数据。
    配置文件server.properties 
    1、为了减少磁盘写入的次数,broker会将消息暂时buffer起来,当消息的个数达到一定阀值或者过了一定的时间间隔时,再flush到磁盘,这样减少了磁盘IO调用的次数。
     配置：Log Flush Policy
     #log.flush.interval.messages=10000   一个分区的消息数阀值
     #log.flush.interval.ms=1000    
   2、kafka的消息保存一定时间（通常为7天）后会被删除。
     配置：Log Retention Policy 
     log.retention.hours=168 
     #log.retention.bytes=1073741824
     log.retention.check.interval.ms=300000
 2. Topic
    主题，即kafka消息的分类。物理上不同的topic的消息是分开存储的，逻辑上一个topic消息虽然于一个或多个broker上但用户只需指定消息的topic即可生产或者消费数据而不必关心数据存在哪里；通常情况下topic是按业务类型做消息topic的区分；
3. Partition
    分区，topic的物理上的分组。每个Topic包含一个或多个Partition.   
    每个partition在存储层面是append log文件。新消息都会被直接追加到log文件的尾部，每条消息在log文件中的位置称为offset（偏移量）。
4. Producer
    消息和数据的生产者，想kafka的一个topic发布消息。
    配置文件：producer.properties
    1、自定义partition
    Producer也根据用户设置的算法来根据消息的key来计算输入哪个partition：partitioner.class
    2、异步或者同步发送
    配置项：producer.type
    异步或者同步发送
    同步是指：发送方发出数据后，等接收方发回响应以后才发下一个数据的通讯方式。  
    异步是指：发送方发出数据后，不等接收方发回响应，接着发送下个数据的通讯方式。
    3、批量发送可以很有效的提高发送效率。
    Kafka producer的异步发送模式允许进行批量发送，先将消息缓存在内存中，然后一次请求批量发送出去。
   具体配置queue.buffering.max.ms、queue.buffering.max.messages。
   默认值分别为5000和10000
5. Consumer
    消息和数据的消费者，定于topic并处理其发布的消息。
    配置文件：consumer.properties
    1、每个consumer属于一个consumer group，可以指定组id。group.id
    2、消费形式：
   组内：组内的消费者消费同一份数据；同时只能有一个consumer消费一个Topic中的1个partition；一个consumer可以消费多个partitions中的消息。
     所以，对于一个topic,同一个group中推荐不能有多于partitions个数的consumer同时消费,否则将意味着某些consumer将无法得到消息。
   组间：每个消费组消费相同的数据，互不影响。
    3、在一个consumer多个线程的情况下，一个线程相当于一个消费者。
   例如：partition为3，一个consumer起了3个线程消费，另一个后来的consumer就无法消费。

    （这是Kafka用来实现一个Topic消息的广播（发给所有的Consumer）和单播（发给某一个Consumer）的手段。
    一个Topic可以对应多个Consumer Group。如果需要实现广播，只要每个Consumer有一个独立的Group就可以了。
    要实现单播只要所有的Consumer在同一个Group里。用Consumer Group还可以将Consumer进行自由的分组而不需要多次发送消息到不同的Topic。）
6. Consumer Group
    消费组，每个Consumer属于一个特定的Consumer Group（可为每个Consumer指定group name，若不指定group name则属于默认的group）。
7. Message
    消息，通信基本单位。每个消息都属于一个partition
    每条Message包含了以下三个属性：
    1°、offset 对应类型：long  此消息在一个partition中序号。可以认为offset是partition中Message的id
    2°、MessageSize  对应类型：int32 此消息的字节大小。
    3°、data  是message的具体内容。
8. zookeeper 
    协调kafka的正常运行。kafka集群管理。
    
```
3. zookeeper和kafka的关系

```
1. zookeeper管理和协调kafka集群；
2. kafka使用zookeeper来存储一些meta信息，，并使用了zookeeper watch 机制来发现meta信息的变更并作出相应的动作；
3. broker node registry : 当kafka的broker启动后，会向zookeeper注册自己的节点信息（临时znode）,当broker与zookeeper断开时，此znode会被删除；
4. broker topic registry : 当一个broker启动后，同样也会向zookeeper注册自己持有的topic和partition；
5. consumer和consumer group：一个group中的多个consumer可以交错的消费一个topic的所有partitions；保证此topic的所有partitions都能被此group所消费，且消费时为了性能考虑，让partition相对均衡的分散到每个consumer上；
6. 状态同步服务；
    1. consumer 会保存消费信息的offset在zookeeper上；
    2. partition的leader注册在zookeeper中，producer作为zookeeper的client，注册了watch用来监听parttion leader的变更事件；
    3. zookeeper支持kafka的parttion的leader和follower的协同和选举，保证parttion中只要leader/follower中只要一个正常，服务就不会中断；
```
producer端使用zookeeper用来“发现”broker列表，以及和topic下每个parttion leader建立socket连接并发送消息；
broker端使用zookeeper用来注册broker信息以及监测parttion leader的存活性；
consumer端使用zookeeper用来注册consumer信息，其中包括consumer消费的partition列表等，同时也用来发现broker列表，并和partition leader建立socket连接，并获取信息；


4. producer消息路由

```
producer发送消息到broker时，会根据partition机制选择将其存储在哪一个Partition.partition机制算法是一种负载均衡的算法使得所有消息均匀分布在不同的partition中。
如果一个Topic对应一个文件，那这个文件所在的机器I/O将会成为这个Topic的性能瓶颈，而有了Partition后，不同的消息可以并行写入不同broker的不同Partition里，极大的提高了吞吐率。
可以在$KAFKA_HOME/config/server.properties中通过配置项num.partitions来指定新建Topic的默认Partition数量，也可在创建Topic时通过参数指定，同时也可以在Topic创建之后通过Kafka提供的工具修改。

在发送一条消息时，可以指定这条消息的key，Producer根据这个key和Partition机制来判断应该将这条消息发送到哪个Parition。
Paritition机制可以通过指定Producer的paritition. class这一参数来指定，该class必须实现kafka.producer.Partitioner接口。
本例中如果key可以被解析为整数则将对应的整数与Partition总数取余，该消息会被发送到该数对应的Partition。（每个Parition都会有个序号,序号从0开始）

import kafka.producer.Partitioner;
import kafka.utils.VerifiableProperties;

public class JasonPartitioner<T> implements Partitioner {

    public JasonPartitioner(VerifiableProperties verifiableProperties) {}

    @Override
    public int partition(Object key, int numPartitions) {
        try {
            int partitionNum = Integer.parseInt((String) key);
            return Math.abs(Integer.parseInt((String) key) % numPartitions);
        } catch (Exception e) {
            return Math.abs(key.hashCode() % numPartitions);
        }
    }
}
```

5. 消息的p2p和发布-订阅

```
kafka是使用consumer group来分别实现这两种方式的，每一个consumer都属于一个consumer group。也就是说每个group中可以有多个consumer。发送到topic的消息，只会被订阅此topic的每个group每个group中的一个consumer消费。一个partition中消息只会被group中的一个consumer消费；一个group可以认为是一个订阅者。
如果所有的consumer都具有相同的group，这种情况和queue模式很像，消息将会在consumers之间负载均衡，这种就是p2p点对点的消息队列；
如果所有的consumer都具有不同的group，那就是f发布-订阅，消息将会广播给所有消费者。
```

6. kafka的数据处理
> 1. 可以使用storm这种实时流处理系统对消息进行实时在线处理；
> 2. 同时可以使用Hadoop 这种批处理系统进行离线处理；
> 3. 同时还可以将数据实时备份到另一个数据中心


- kafka什么时候用
1. 从不同的角度出发，kafka的作用也不同。
> 1. kafka作为一个**消息系统**，类比redis,rabbitmq等同类产品，
> 2. kafka作为一个**存储系统**，因为进入kafak的消息都会被持久化所以可以作为一个存储系统，
> 3. kafka作为一个**流处理系统**，对于流数据，可以在kafka里多次处理，多次流转。
2. 相应的，kafka 的使用场景大致如下：
> 1. 作为传统消息队列系统的替换。
> 2. 做metric监控数据的收集处理。
> 3. 做日志数据的收集处理。
流数据处理。
> 4. 事件驱动架构的核心组件。

- kafka怎么用
1. 安装测试

```
// step1 下载
wget http://mirrors.shuosc.org/apache/kafka/1.0.0/kafka_2.11-1.0.0.tgz

// step2 解压
tar -zxvf kafka_2.11-1.0.0.tgz
cd /usr/local/kafka_2.11-1.0.0/

// step3 配置
vim /usr/local/kafka/config/server.properties

// step4 修改
broker.id=1
log.dir=/data/kafka/logs-1

// step5 启动 zk
bin/zookeeper-server-start.sh -daemon config/zookeeper.properties

// step6 启动Kafka 服务
bin/kafka-server-start.sh config/server.properties

// step7 创建topic
bin/kafka-topics.sh --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic test

// step8 查看topic列表
bin/kafka-topics.sh --list --zookeeper localhost:2181

// step9 使用producer控制台
bin/kafka-console-producer.sh --broker-list localhost:9092 --topic test

// step10 使用consumer控制台
bin/kafka-console-consumer.sh --zookeeper localhost:2181 --topic test --from-beginning

// step11 查看topics信息
bin/kafka-topics.sh --describe --zookeeper localhost:2181 --topic test

```

2. 集群配置

```
1. 单机多个broker集群配置
    利用单节点部署多个broker，不同的broker设置不同的id，监听端口以及日志记录；
    分别新建两个server-2.properties和server-3.properties
// server-2.properties
broker.id=2
listeners = PLAINTEXT://your.host.name:9093
log.dir=/data/kafka/logs-2
// server-3.properties
broker.id=3
listeners = PLAINTEXT://your.host.name:9094
log.dir=/data/kafka/logs-3
    启动kafka
bin/kafka-server-start.sh config/server-2.properties &
bin/kafka-server-start.sh config/server-3.properties &

2. 多机多broker集群配置
    分别在多个节点按上述方式安装 Kafka，配置启动多个 Zookeeper 实例。
    假设三台机器 IP 地址是 ： 192.168.153.135， 192.168.153.136， 192.168.153.137
    分别配置多个机器上的 Kafka 服务，设置不同的 broker id，zookeeper.connect config/server.properties设置如下:
zookeeper.connect=192.168.153.135:2181,192.168.153.136:2181,192.168.153.137:2181

    
```

