- 部署环境
> 1. broker: 三台broker01,broker02,broker03；
> 2. zookeeper: 两台zookeeper
> 3. kafka-manager: 一台
> 4. kafka-offser-monitor: 一台


 容器名 | IP | 功能
---|---|---
zookeeper01  | 172.100.0.10  |  zookeeper 服务1
zookeeper02  | 172.100.0.11  |  zookeeper 服务2
broker01  |  172.100.0.12  |  kafka 集群broker1
broker02  |  172.100.0.13  |  kafka 集群broker2 
broker03  |  172.100.0.14  |  kafka 集群broker3
kafka-manger  |  172.100.0.15  |  kafka manager 服务
kafka-offset-monitor  |  172.100.0.16  |  kafka offset monitor 服务


- docker-compose.yml

```
zookeeper01:
    image: gavingao/zookeeper:init
    restart: always
    ports:
      - "2181:2181" 
    volumes:
      - ${PWD}/kafka/zookeeper01/data:/data
      - ${PWD}/log/kafka/zookeeper01:/datalog
    environment:
      ZOO_MY_ID: 1
      ZOO_SERVERS: server.1=zookeeper01:2888:3888 server.2=zookeeper02:2888:3888
    networks:
      docker_net:
        ipv4_address: ${ZOOKEEPER01_IP}

  zookeeper02:
    image: gavingao/zookeeper:init
    restart: always
    ports:
      - "2182:2181" 
    volumes:
      - ${PWD}/kafka/zookeeper02/data:/data
      - ${PWD}/log/kafka/zookeeper02:/datalog
    environment:
      ZOO_MY_ID: 2
      ZOO_SERVERS: server.1=zookeeper01:2888:3888 server.2=zookeeper02:2888:3888
    networks:
      docker_net:
        ipv4_address: ${ZOOKEEPER02_IP}

  broker01:
    image: gavingao/kafka:init
    restart: always
    depends_on: [zookeeper01,zookeeper02]
    hostname: broker01
    expose:
      - "9091"
    ports:
      - "9091:9091"
    volumes: 
      - ${PWD}/kafka/broker01:/kafka
    environment:
       KAFKA_BROKER_ID: 1
       KAFKA_ADVERTISED_HOST_NAME: broker01
       KAFKA_ADVERTISED_PORT: 9091
       KAFKA_ZOOKEEPER_CONNECT: zookeeper01:2181,zookeeper02:2181
       KAFKA_LISTENERS: PLAINTEXT://broker01:9091
       KAFKA_ADVERTISED_LISTENERS: PLAINTEXT://broker01:9091
    networks:
      docker_net:
        ipv4_address: ${BROKER01_IP}

  broker02:
    image: gavingao/kafka:init
    restart: always
    depends_on: [zookeeper01,zookeeper02]
    hostname: broker02
    expose:
      - "9092"
    ports:
      - "9092:9092"
    volumes: 
      - ${PWD}/kafka/broker02:/kafka
    environment:
       KAFKA_BROKER_ID: 1
       KAFKA_ADVERTISED_HOST_NAME: broker02
       KAFKA_ADVERTISED_PORT: 9092
       KAFKA_ZOOKEEPER_CONNECT: zookeeper01:2181,zookeeper02:2181
       KAFKA_LISTENERS: PLAINTEXT://broker02:9092
       KAFKA_ADVERTISED_LISTENERS: PLAINTEXT://broker02:9092
    networks:
      docker_net:
        ipv4_address: ${BROKER02_IP}

  broker03:
    image: gavingao/kafka:init
    restart: always
    depends_on: [zookeeper01,zookeeper02]
    hostname: broker03
    expose:
     - "9093"
    ports:
     - "9093:9093"
    volumes: 
     - ${PWD}/kafka/broker03:/kafka
    environment:
       KAFKA_BROKER_ID: 1
       KAFKA_ADVERTISED_HOST_NAME: broker03
       KAFKA_ADVERTISED_PORT: 9093
       KAFKA_ZOOKEEPER_CONNECT: zookeeper01:2181,zookeeper02:2181
       KAFKA_LISTENERS: PLAINTEXT://broker03:9093
       KAFKA_ADVERTISED_LISTENERS: PLAINTEXT://broker03:9093
    networks:
      docker_net:
        ipv4_address: ${BROKER03_IP}

   kafka-manager:
    image: sheepkiller/kafka-manager
    restart: always
    expose:
      - "9100"
    ports: 
      - "9100:9100"
    depends_on: [zookeeper01,zookeeper02,broker01,broker02,broker03]
    environment:
      ZK_HOSTS: zookeeper01:2181,zookeeper02:2181   
    command: -Dhttp.port=9100 
    networks:
      docker_net:
        ipv4_address: ${KAFKA_MANAGER_IP}

  kafka-offset-monitor:
    image: hwestphal/kafka-offset-monitor
    restart: always
    expose:
      - "9101"
    ports:
      - "9101:9101"
    depends_on: [zookeeper01,zookeeper02,broker01,broker02,broker03]
    volumes:
      - ${PWD}/log/kafka/kafka-offset-monitor:/u01/app/kafka-offset-monitor/logs/
    environment:
      ZK_HOSTS: zookeeper01:2181,zookeeper02:2181   
      KAFKA_BROKERS: broker01:9091,broker02:9092,broker03:9093
      REFRESH_SECENDS: 10
      RETAIN_DAYS: 2
    networks:
      docker_net:
        ipv4_address: ${KAFKA_OFFSET_MONITOR_IP}  
```

- 遇到错误
1. kafka-offset-monitor 启动失败
> 进入容器，手动启动：

```
java -cp /app.jar com.quantifind.kafka.offsetapp.OffsetGetterWeb --zk zookeeper01:2181,zookeeper02:2181 --port 9101 --refresh 10.seconds --retain 2.days &
```


