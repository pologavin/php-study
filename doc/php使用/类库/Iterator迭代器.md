> 又称游标，迭代器模式一种软件设计模式。为容器物件（list，array，vector等）遍历提供接口，而不暴露该对象内部细节。   
> php5+ spl 中提供了迭代器接口 Iterator，要实现迭代器模式，实现该接口即可。


- 迭代器使用场景
    1. 使用返回迭代器的包或库时
    2. 无法在一次的调用获取容器的所有元素时
    3. 要处理数量巨大的无素时（数据库中的表以GB计的数据），比如读取日志文件;
    4. 多任务调度。
    5. 放在循环中迭代数据。
    

- 迭代器协议

```
Iterator extends Traversable {
/* 方法 */
  abstract public mixed current ( void ) // 返回当前元素
  abstract public scalar key ( void )    // 返回当前元素的键
  abstract public void next ( void )     // 向前移动到下一个元素
  abstract public void rewind ( void )   // 返回到迭代器的第一个元素
  abstract public boolean valid ( void ) //  检查当前位置是否有效
}
```

- 生成器Generator 
> 实现了迭代器协议的一种对象。这种对象比较特殊：
>    1. 无法直接使用 new 实例化
>    2. 得使用 yield 产生
>    3. 可以通过send方法传值给yield所在位置

```
Generator implements Iterator {
/* 方法 */
public current ( void ) : mixed //返回当前产生的值
public key ( void ) : mixed //返回当前产生的键
public next ( void ) : void // 生成器继续执行
public rewind ( void ) : void // 重置迭代器
public send ( mixed $value ) : mixed // 向生成器中传入一个值
public throw ( Exception $exception ) : void // 向生成器中抛入一个异常
public valid ( void ) : bool // 检查迭代器是否被关闭
public __wakeup ( void ) : void // 序列化回调
}
```

- 引入yield
1. 读文件常规实现

```
// 使用数组.
function getLines($file) {
  $f = fopen($file, 'r');
  $lines = [];
  if (!$f) throw new Exception();
  while (false !== $line = fgets($f)) {
    $lines[] = $line;
  }
  fclose($f);
  return $lines;
}
```
2. 使用迭代器实现，可以解决文件过大内存爆掉问题。

```
// 自己实现文件遍历
class FileIterator implements Iterator {
    protected $f;
    protected $data;
    protected $key;
    public function __construct($file) {
        $this->f = fopen($file, 'r');
        if (!$this->f) throw new Exception();
    }
    public function __destruct() {
        fclose($this->f);
    }
    public function current() {
        return $this->data;
    }
    public function key() {
        return $this->key;
    }
    public function next() {
        $this->data = fgets($this->f);
        $this->key++;
    }
    public function rewind() {
        fseek($this->f, 0);
        $this->data = fgets($this->f);
        $this->key = 0;
    }
    public function valid() {
        return false !== $this->data;
    }
}
```

3. 这种模式代码过于复杂，于是php5.5引入yield关键字。

```
// 使用 yield 完成相同功能
function getLines($file) {
    $f = fopen($file, 'r');
    if (!$f) throw new Exception();
    while ($line = fgets($f)) {
        yield $line;
    }
    fclose($f);
}
```
