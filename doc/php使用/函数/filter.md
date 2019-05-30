> php 自带的过滤器。

- filter_input 

```
mixed filter_input ( int $type , string $variable_name [, int $filter = FILTER_DEFAULT [, mixed $options ]] )

// 参数：
1. type:
    INPUT_GET,INPUT_POST, INPUT_COOKIE, INPUT_SERVER或 INPUT_ENV之一。
2. variable_name : 
    带获取的变量名
3. filter：
    过滤器ID：默认：FILTER_UNSAFE_RAW
     1. FILTER_SANITIZE_EMAIL : 验证邮箱；
     2. FILTER_SANITIZE_ENCODED : url转码；
     3. FILTER_SANITIZE_MAGIC_QUOTES : 反斜杠转义，类似addslashes处理；
     4. FILTER_SANITIZE_NUMBER_FLOAT : 删除浮点数中所有非法的字符。
     5. FILTER_SANITIZE_NUMBER_INT : 删除数字中所有非法的字符。
     6. FILTER_SANITIZE_SPECIAL_CHARS : 对特殊字符进行 HTML 转义。
     7. FILTER_SANITIZE_FULL_SPECIAL_CHARS : 对特殊字符全部进行 HTML 转义，类似htmlspecialchars；
     8. FILTER_SANITIZE_STRING ； 去除或编码不需要的字符。
     9. FILTER_SANITIZE_STRIPPED : 去除或编码不需要的字符。
     10. FILTER_SANITIZE_URL : 删除字符串中所有非法的 URL 字符。
     11. FILTER_UNSAFE_RAW : 不进行任何过滤，去除或编码特殊字符。
```

- filter_input_array
> 批量filter_input
```
mixed filter_input_array ( int $type [, mixed $definition [, bool $add_empty = true ]] )
```


- filter_var
> 对变量进行过滤，用法类似。

```
mixed filter_var ( mixed $variable [, int $filter = FILTER_DEFAULT [, mixed $options ]] )
```


- filter_var_array
> 批量filter_var。

```
mixed filter_var_array ( array $data [, mixed $definition [, bool $add_empty = true ]] )
```
