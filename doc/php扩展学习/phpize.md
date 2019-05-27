- phpize 是什么
1. 编译PHP扩展工具，主要是根据系统信息生成对应的configure文件。
2. 他是一个shell脚本文件。
3. 使用phpize可以外挂php扩展模块；

- phpize 怎么使用
1. 进入安装包目录，执行phpize；
2. 生产configure文件，执行./configure –with-php-config=path/php-config
3. 执行make
4. 执行make && install 