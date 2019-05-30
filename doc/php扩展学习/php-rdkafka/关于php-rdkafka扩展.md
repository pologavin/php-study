- php-rdkafka是什么
1. kafka服务的php扩展，为php提供对接kafka的扩展接口服务。
2. php-rdkafka项目地址：https://github.com/arnaud-lb/php-rdkafka/blob/master/README.md
3. php-rdkafka是依赖Librdkafka（c/c++的kafka sdk）
4. librdkafka项目地址:https://github.com/edenhill/librdkafka


- php-rdkafka 怎么用
1. 安装

```
1. dev环境准备
sudo yum install zlib zlib-devel openssl openssl-devel cyrus-sasl2 cyrus-sasl-devel

2. 安装librdkafka
wget -c https://github.com/edenhill/librdkafka/archive/v0.11.0.tar.gz（git clone https://github.com/edenhill/librdkafka/）
tar xvzf v0.11.0.tar.gz
cd librdkafka
./configure
make 
sudo make install

3. 安装php-rdkafka
wget -c https://github.com/arnaud-lb/php-rdkafka/archive/3.0.4.tar.gz（git clone https://github.com/arnaud-lb/php-rdkafka.git）
tar xvzf 3.0.4.tar.gz
cd php-rdkafka
phpize
./configure
make all -j 5
sudo make install
sh -c 'echo "extension=rdkafka.so" >> /usr/local/php/etc/php.ini'

// 查看php-rdkafka配置
php -m | grep kafka

```

4. PHP使用kafka

```
1. Rdkafka提供很多接口，具体文档：https://arnaud-lb.github.io/php-rdkafka/phpdoc/book.rdkafka.html

2. connect链接
$this->rk = null;

$kafkaConf = new \RdKafka\Conf();
$kafkaConf->set('broker.version.fallback', $this->brokerVersionFallback);
$kafkaConf->set('socket.blocking.max.ms', 1);
$kafkaConf->setDrMsgCb(array($this, 'produceDrMsgCallback'));
$kafkaConf->setErrorCb(array($this, 'produceErrorCallback'));

$rk = new \RdKafka\Producer($kafkaConf);
$rk->setLogLevel($this->logLevel);
$rk->setLogger($this->logger);
$rk->addBrokers(implode(',', $this->brokers));

$this->rk = $rk;
$this->lastConnectTime = time();

3. produce生产
$this->connect();
$topicConf = new \RdKafka\TopicConf();
$topicConf->set("message.timeout.ms", $this->messageTimeoutMs);
$topic = $this->rk->newTopic($topic, $topicConf);

$topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload, $key);

//阻塞等待，为保证函数结束前已调用DrMsgCb返回发送结果
$this->rk->poll($this->messageTimeoutMs + 500); 

4. consumer消费
if (empty($this->groupId) || empty($this->topic)) {
    throw new Exception("please set groupId and topic to this consumer");
}
$this->rk = new Rdkafka\Consumer($this->conf);
$brokerList = $this->zkUtils->getBrokerList();
if ($brokerList == "") {
    throw new Exception ("broker list is empty!");
}
$this->rk->addBrokers($brokerList);

$topicConf = new Rdkafka\TopicConf();
$topicConf->set('auto.offset.reset', $this->offsetAutoReset);
$this->rkTopic = $this->rk->newTopic($this->topic, $topicConf);

$this->lastCommitTime = $this->getTime();
if (!$this->zkUtils->registerConsumer($this->topic, $this->groupId, $this->consumerId)) {
    throw new Exception("start failed");
    exit;
}

$this->lastWatchTime = 0;
while (self::$running) {
    $this->consume($callback_func);
    pcntl_signal_dispatch();
}
$this->shutdown();
```

- kafka代理kafkaproxy
1.问题： php的kafka链接是短连接，这中间的数据传输和网络请求效率很低；
2. 解决方案：kafka和php添加一层长连接代理，使用redis数据协议进行传输，这样很好解决这个瓶颈问题；



- Rdkafka 的ide-helper
> https://github.com/kwn/php-rdkafka-stubs