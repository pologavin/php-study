
- 安装最新版本openssl

```
git clone https://github.com/openssl/openssl.git
cd openssl
./config --prefix=/usr/local/openssl shared no-zlib
make
make install
```

- 升级符号链接

```
mv /usr/bin/openssl /usr/bin/openssl.bak   
ln -s /usr/local/openssl/bin/openssl  /usr/bin/openssl
ln -s /usr/local/openssl/lib/libssl.so.3 /usr/lib/x86_64-linux-gnu/libssl.so.3
ln -s /usr/local/openssl/lib/libcrypto.so.3 /usr/lib/x86_64-linux-gnu/libcrypto.so.3
```

- 查看安装后版本

```
openssl version
which openssl
```
