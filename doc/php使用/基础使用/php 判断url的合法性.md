1. 使用filter_var进行写法合法规则判断；

```
filter_var($string, FILTER_VALIDATE_URL);
// 如果返回false，即验证失败；
```

2. get_header 判断真实有效性

```
$header = get_headers($url);
if (!preg_grep("/200/", $header)) {
    return false;
}
```

