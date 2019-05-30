PHP spl数据结构学习   

PHP-MANUAL :https://www.php.net/manual/zh/spl.datastructures.php


> SPL提供了一套标准的数据结构，这些都是在应用开发过程中的常用数据结构，如双向链表、堆、栈等。

### SplDoublyLinkedList
> 双链表是一种重要的线性存储结构，对于双链表中的每个节点，不仅仅存储自己的信息，还要保存前驱和后继节点的地址。
1. 双向链表的数据结构由一个双向链表类（spl_dllist_object）、一个双向链表（spl_ptr_llist） 和一个标准的双向链表元素结构体（spl_ptr_llist_element）组成 这三者存在着包含关系.
2.  双向链表类实现了迭代器接口，我们可以直接用foreach遍历整个链表.
3.  其实现了 Countable接口，即实现了count方法，我们可以直接对spl_dllist_object使用count函数获取链表中的元素个数.
4. 实现了 ArrayAccess 接口，可以如数组般访问链表数据；


```
SplDoublyLinkedList  implements Iterator   , ArrayAccess   , Countable   {
 
     /*
      # 关于模式
      IT_MODE_LIFO: Stack style, 后入先出，堆结构
      IT_MODE_FIFO: Queue style, 先入先出，队列结构(默认)
      IT_MODE_DELETE: Elements are deleted by the iterator 一边迭代，一边删除
      IT_MODE_KEEP: Elements are traversed by the iterator 普通迭代，不删除(默认)
     */
     
    const integer IT_MODE_LIFO = 2 ;
    const integer IT_MODE_FIFO = 0 ;
    const integer IT_MODE_DELETE = 1 ;
    const integer IT_MODE_KEEP = 0 ;

    public __construct  ( void )
    public void add  ( mixed  $index  , mixed  $newval  )
    //双链表的末尾节点
    public mixed top  ( void )
    //双链表的开头节点
    public mixed bottom  ( void )
    //双联表元素的个数
    public int count  ( void )
    //检测双链表是否为空
    public bool isEmpty  ( void )
 
    //当前节点索引
    public mixed key  ( void )
    //移到上条记录
    public void prev  ( void )
    //移到下条记录
    public void next  ( void )
    //当前记录
    public mixed current  ( void )
    //将指针指向迭代开始处
    public void rewind  ( void )
    //检查双链表是否还有节点
    public bool valid  ( void )
 
    //指定index处节点是否存在
    public bool offsetExists  ( mixed  $index  )
    //获取指定index处节点值
    public mixed offsetGet  ( mixed  $index  )
    //设置指定index处值
    public void offsetSet  ( mixed  $index  , mixed  $newval  )
    //删除指定index处节点
    public void offsetUnset  ( mixed  $index  )
 
    //从双链表的尾部弹出元素
    public mixed pop  ( void )
    //添加元素到双链表的尾部
    public void push  ( mixed  $value  )
 
    //序列化存储
    public string serialize  ( void )
    //反序列化
    public void unserialize  ( string $serialized  )
 
    //设置迭代模式
    public void setIteratorMode  ( int $mode  )
    //获取迭代模式SplDoublyLinkedList::IT_MODE_LIFO  (Stack style) SplDoublyLinkedList::IT_MODE_FIFO  (Queue style)
    public int getIteratorMode  ( void )
 
    //双链表的头部移除元素
    public mixed shift  ( void )
    //双链表的头部添加元素
    public void unshift  ( mixed  $value  )
 
}
```

### SplStack
> 继承SplDoublyLinkedList; IT_MODE_LIFO: Stack style;


```
class SplStack extends SplDoublyLinkedList {

    public function setIteratorMode ($mode) {}
}
```

### SplQueue
> 继承SplDoublyLinkedList; IT_MODE_FIFO: Queue style;

```
class SplQueue extends SplDoublyLinkedList {

    //入队 SplDoublyLinkedList::push()别称
    public function enqueue ($value) {}
    // 出队 SplDoublyLinkedList::shift()别称
    public function dequeue () {}

    public function setIteratorMode ($mode) {}

}
```

### SplHeap
> 二叉堆的实现。堆是一颗完全二叉树，常用于管理算法执行过程中的信息，应用场景包括堆排序，优先队列等。分为大头堆和小头堆，在定义上的区别是父节点的值是大于还是小于子节点的值，spl中堆、大头堆、小头堆和优先队列是同一类数据结构。

1. SplHeap是抽象类，所以要先继承，实现里面的抽象方法compare后，才能new对象使用。
2. compare这个方法决定堆的排序规则；

```
1. 大头堆：return $value1 < $value2 ? -1 : 1;
2. 小头堆：$value1 > $value2 ? -1 : 1;
3. 如果是数组，需要定制使用key还是value排序；
4. 更为复杂的排序，可以定制重写compare方法
```


```

abstract SplHeap implements Iterator , Countable {
    // 创建一个空堆
    public __construct ( void )
    // 比较两个节点的大小
    abstract protected int compare ( mixed $value1 , mixed $value2 )
    // 返回堆节点数
    public int count ( void )
    // 返回迭代指针指向的节点
    public mixed current ( void )
    // 从堆顶部提取一个节点并重建堆
    public mixed extract ( void )
    // 向堆中添加一个节点并重建堆
    public void insert ( mixed $value )
    // 判断是否为空堆
    public bool isEmpty ( void )
    // 返回迭代指针指向的节点的键
    public mixed key ( void )
    // 迭代指针指向下一节点
    public void next ( void )
    // 恢复堆
    public void recoverFromCorruption ( void )
    // 重置迭代指针
    public void rewind ( void )
    // 返回堆的顶部节点
    public mixed top ( void )
    // 判断迭代指针指向的节点是否存在
    public bool valid ( void )
```

### SplMaxHeap
> 大头堆。splHeap的大到小的排序堆实现。

### SplMinHeap
> 小头堆。splHeap的小到大的排序堆实现。


### SplPriorityQueue
> 优先级队列是不同于先进先出队列的另一种队列，它每次从队列中取出的是具有最高优先权的元素， 这里的优先是指元素的某一属性优先，以比较为例，可能是较大的优先，也可能是较小的优先。

1. PHP实现的优先级队列默认是以大头堆实现，即较大的优先，如果要较小的优先，则需要继承SplPriorityQueue类，并重载compare方法。


```
class SplPriorityQueue implements Iterator, Countable {
    /**
     * EXTR_DATA  // 只提取数据(默认)
       EXTR_PRIORITY // 只提取优先级
       EXTR_BOTH // 优先级数据两者提取
    **/
    const EXTR_BOTH = 3;
    const EXTR_PRIORITY = 2;
    const EXTR_DATA = 1;
    
    // 优先级排序规则
    public function compare ($priority1, $priority2) {}
    
    // 设置提取方式
    public function setExtractFlags ($flags) {}
     
    // 获取提取方式   
    public function recoverFromCorruption () {}
    
    ... 
}
```


### SplFixedArray
> SplFixedArray类提供了数组的主要功能。 SplFixedArray和普通PHP数组之间的主要区别在于SplFixedArray具有固定长度，并且仅允许范围内的整数作为索引。优点是它允许更快的阵列实现。

1. 性能优于array，但是初始化必须设置长度，而且key必须是整数。


### SplObjectStorage
> 提供从对象到数据的映射。 实现了对象存储映射表，应用于需要唯一标识多个对象的存储场景。