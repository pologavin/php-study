> 通过使用一个双向链表来提供队列的主要功能。  
> php5.3+ 

- 方法：

```
__construct — 使用双向链表创建新队列
dequeue — 从队列中删除节点
enqueue — 向队列中添加一个元素
setIteratorMode — 设置迭代的模式
```

- SplDoublyLinkedList继承：

```
add ( mixed $index , mixed $newval )
bottom ( void )
count ( void )
current ( void )
getIteratorMode ( void )
isEmpty ( void )
key ( void )
next ( void )
offsetExists ( mixed $index )
offsetGet ( mixed $index )
offsetSet ( mixed $index , mixed $newval )
offsetUnset ( mixed $index )
pop ( void )
prev ( void )
push ( mixed $value )
rewind ( void )
serialize ( void )
setIteratorMode ( int $mode )
shift ( void )
top ( void )
unserialize ( string $serialized )
unshift ( mixed $value )
valid ( void )

```
