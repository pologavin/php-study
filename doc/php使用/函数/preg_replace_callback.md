
- 执行一个正则表达式搜索并且使用一个回调进行替换
- demo

```
$str="    　PHP 函数很强大 　　　　";
$str = preg_replace_callback(['/^[\s|　]+/','/[\s|　]+$/'], function($matches){
    return str_repeat('☐', mb_strlen($matches[0]));
}, $str);
echo $str . "\r\n";
```
