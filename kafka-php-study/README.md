kafka-php-study

Kafka-php 使用纯粹的PHP 编写的 kafka 客户端，目前支持 0.8.x 以上版本的 Kafka，该项目 v0.2.x 和 v0.1.x 不兼容，如果使用原有的 v0.1.x 的可以参照文档 [Kafka PHP v0.1.x Document](https://github.com/weiboad/kafka-php/blob/v0.1.6/README.md), 不过建议切换到 v0.2.x 上。v0.2.x 使用 PHP 异步执行的方式来和kafka broker 交互，较 v0.1.x 更加稳定高效, 由于使用 PHP 语言编写所以不用编译任何的扩展就可以使用，降低了接入与维护成本

[Github地址](https://github.com/weiboad/kafka-php)