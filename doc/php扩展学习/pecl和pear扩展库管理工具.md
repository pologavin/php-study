- pear 是什么
1. the PHP Extension and Application Repository 的缩写，php扩展及应用的代码仓库

- pear 做什么
1. 按照pear编码规则编写php扩展；
2. 编程语言：php；
3. 官方扩展库：https://pear.php.net/packages.php

- pear/pecl 安装

```
#这是一个安装 pear 的 php 发行包文件
wget http://pear.php.net/go-pear.phar
/1. curl -O http://pear.php.net/go-pear.phar
#执行安装
php go-pear.phar
/ 2. sudo php -d detect_unicode=0 go-pear.phar
3. 输入1，回车，配置pear路径为：/usr/local/pear
4. 输入4，回车，配置命令路径为：/usr/local/bin
5. 回车两次，其他让其默认，安装完成
6. 可以通过命令检查pear安装是否成功`pear version`



#如果想升级到最新版本
pear channel-update pecl.php.net
#更新下仓库
pecl channel-update pecl.php.net
```

- pear 使用

```
pear install 扩展名
```




- PECL 是什么
1. PHP Extension Community Library php 的 C 扩展仓库，即 php 的 so 格式的扩展
2. 是一个开放的并通过 PEAR(PHP Extension and Application Repository，PHP 扩展和应用仓库)打包格式来打包安装的 PHP 扩展库仓库。
3. 与pear不同的是：PECL 扩展包含的是可以编译进 PHP Core 的 C 语言代码，因此可以将 PECL 扩展库编译成为可动态加载的 .so 共享库，或者采用静态编译方式与 PHP 源代码编译为一体的方法进行扩展。PECL 扩展库包含了对于 XML 解析，数据库访问，邮件解析，嵌入式的 Perl 以及 Pthyon 脚本解释器等诸多的 PHP 扩展模块，因此从某种意义上来说，在运行效率上 PECL 要高于以往诸多的 PEAR 扩展库。

