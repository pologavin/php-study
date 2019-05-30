- msgpack是什么
1. 一种高效二进制序列化的数据格式，跟json类似，可以在多个语言中进行相互数据交换。
2. 这种格式**小巧快速**，多个小整数会压缩成一个字节，通常短字符串压缩后只比原来长度增加1个字节。MessagePack支持超过50种编程语言和环境
3. 官方地址：https://msgpack.org/
4. php项目地址：https://github.com/laruence/msgpack-php

- msgpack的fast
![image](http://www.gavin.xin/wp-content/uploads/2018/08/afb35d36-7aa3-3bc8-af7f-0d4360c096de-300x181.png)
1. json的解析一般都是使用cJson这个库，cJSON存储的时候是**采用链表存储**的，其访问方式很像一颗树。每一个节点可以有兄妹节点，通过next/prev指针来查找，它类似双向链表；每个节点也可以有孩子节点，通过child指针来访问，进入下一层。问题就是首先，构造这个链表的时候，得一个字符一个字符地匹配过去吧，得判断是不是引号、括号之类的吧…
2. MessagePack是直接一遍遍历过去了，从前面的数据头，就可以知道后面的是什么数据，指针应该向后移动多少，比JSON的构建链表少了很多比较的过程。平均解析速度快70%左右；

- msgpack的small
1. msgpack是使用一种压缩算法来做数据存储。官方的数据表示结构文档：https://gist.github.com/frsyuki/5432559 总结起来一下几点：

```
1.true、false 之类的：这些太简单了，直接给1个字节，（0xc2 表示true，0xc3表示false）

2.不用表示长度的：就是数字之类的，他们天然是定长的，是用一个字节表示后面的内容是什么东东，比如用（0xcc 表示这后面，是个uint 8，用oxcd表示后面是个uint 16，用 0xca 表示后面的是个float 32).

3.不定长的：比如字符串、数组，类型后面加 1~4个字节，用来存字符串的长度，如果是字符串长度是256以内的，只需要1个字节，MessagePack能存的最长的字符串，是(2^32 -1 ) 最长的4G的字符串大小。

4.ext结构：表示特定的小单元数据。

5.高级结构：MAP结构，就是key=>val 结构的数据，和数组差不多，加1~4个字节表示后面有多少个项。

```
2. MessagePack对数字、多字节字符、数组等都做了很多优化，减少了无用的字符，二进制格式，也保证不用字符化带来额外的存储空间的增加，所以MessagePack比JSON小是肯定的，小多少，得看你的数据。如果你用来存英文字符串，那几乎是没有区别….
3. 比较实例：![image](http://www.gavin.xin/wp-content/uploads/2018/08/messpack.png)

- messagepack 常用在哪里
1. 用于结构化数据的缓存和存储：
> 1. 存在Memcache中，因为它比json小，可以省下一些内存来，速度也比json快一些，页面速度自然快一个档次。只是json出来的数据直接可以使用，messagepack需要解析后使用；
> 2. 存在可以持久化的Key-val存储中。


- messagepack 应用
1. php 扩展是有鸟哥开发，在yar中也是使用messagepack的打包数据协议的。

```
1. 可以用PECL的安装方式（PHP>7）：
pecl install msgpack
也可以编译源码安装：
$/path/to/phpize
$./configure
$make && make install

2. wget下载
>wget https://pecl.php.net/get/msgpack-0.5.7.tgz
> tar -zxvf msgpack-php-msgpack-0.5.7.tar.gz
> cd cd msgpack-php-msgpack-0.5.7
> phpize
> ./configure --with-php-config=/etc/alternatives/php-config
> make && make install 

3. 使用方式

$data = array(0=>1,1=>2,2=>3);
$msg = msgpack_pack($data);
$data = msgpack_unpack($msg);

```

