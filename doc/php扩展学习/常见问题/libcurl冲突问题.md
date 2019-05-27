### 现象

```
/usr/bin/curl: /usr/local/lib/libcurl.so.4: no version information available (required by /usr/bin/curl)

```

### 问题
> curl 冲突

### 解决方案
1. 首先定位一下 libcurl 的位置：

```
locale libcurl.so.4

/usr/lib/x86_64-linux-gnu/libcurl.so.4
/usr/lib/x86_64-linux-gnu/libcurl.so.4.3.0
/usr/local/lib/libcurl.so.4
/usr/local/lib/libcurl.so.4.4.0
```
2. 将这个冲突的软链接删掉：

```
rm -rf /usr/local/lib/libcurl.so.4
```

3. 将 4.4.0 的静态库链接到上面：

```
ln -s /usr/lib/x86_64-linux-gnu/libcurl.so.4.4.0 /usr/local/lib/libcurl.so.4
```
4. 验证

```
ls -l /usr/local/lib/libcurl.so.4
```
