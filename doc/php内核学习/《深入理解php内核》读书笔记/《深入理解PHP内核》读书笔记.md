### - **PHP的设计理论及特点**  
1.多进程模型：由于PHP是多进程模型，不同请求间互不干涉，这样保证了一个请求挂掉不会对全盘服务造成影响，当然，随着时代发展，PHP也早已支持多线程模型。  
2.弱类型语言：和C/C++、Java、C#等语言不同，PHP是一门弱类型语言。一个变量的类型并不是一开始就确定不变，运行中才会确定并可能发生隐式或显式的类型转换，这种机制的灵活性在web开发中非常方便、高效，具体会在后面PHP变量中详述。  
3.引擎(Zend)+组件(ext)的模式降低内部耦合。  
4.中间层(sapi)隔绝web server和PHP。  
5.语法简单灵活，没有太多规范。缺点导致风格混杂，但再差的程序员也不会写出太离谱危害全局的程序。  

### - **架构体系**  
![image](http://www.gavin.xin/wp-content/uploads/2016/09/1-1.jpg)  

-- 源码目录结构  
--- **/根目录**    
> 包含一些说明文件以及设计方案。                     其实项目中的这些README文件是非常值得阅读的例如：
/README.PHP4-TO-PHP5-THIN-CHANGES 这个文件就详细列举了PHP4和PHP5的一些差异。
还有有一个比较重要的文件/CODING_STANDARDS，如果要想写PHP扩展的话，这个文件一定要阅读一下， 不管你个人的代码风格是什么样，怎么样使用缩进和花括号，既然来到了这样一个团体里就应该去适应这样的规范，这样在阅读代码或者别人阅读你的 代码是都会更轻松。  

---- **build目录**  
> 这里主要放置一些和源码编译相关的一些文件，比如开始构建之前的buildconf脚本等文件，还有一些检查环境的脚本等。  

---- **ext目录**  
> 官方扩展目录，包涵绝大多数php的函数定义和实现，如array系列，pdo系列，spl系列等函数的实现，都在这个目录中。个人写的扩展在测试时也可以放到这个目录，方便测试和调试。  

---- **main目录**  
> 这里存放的就是PHP最为核心的文件了，主要实现PHP的基本设施，这里和Zend引擎不一样，Zend引擎主要实现语言最核心的语言运行环境。  

---- **zend目录**  
> Zend引擎的实现目录，比如脚本的词法语法解析，opcode的执行以及扩展机制的实现等等。  

---- **pear目录**  
> “PHP 扩展与应用仓库”，包含PEAR的核心文件。  

---- **sapi目录**  
> 包含了各种服务器抽象层的接口代码，例如apache的mod_php，cgi，fastcgi以及fpm等等接口。  

---- **TSRM目录**  
> PHP线程安全资源管理器，PHP的线程安全是构建在TSRM库之上的，PHP实现中常见的*G宏通常是对TSRM的封装。  

---- **tests目录**  
> PHP的测试脚本集合，包含PHP各项功能的测试文件  

---- **win32目录**  
> 主要包括Windows平台相关的一些实现，比如sokcet的实现在Windows下和*Nix平台就不太一样，同时也包括了Windows下编译PHP相关的脚本。  



### - **PHP代码执行流程**  
 ![image](http://www.gavin.xin/wp-content/uploads/2016/09/2-1.jpg) 

> 从图上可以看到，PHP实现了一个典型的动态语言执行过程：拿到一段代码后，经过词法解析、语法解析等阶段后，源程序会被翻译成一个个指令(opcodes)，然后ZEND虚拟机顺次执行这些指令完成操作。PHP本身是用C实现的，因此最终调用的也都是C的函数，实际上，我们可以把PHP看做是一个C开发的软件。
PHP的执行的核心是翻译出来的一条一条指令，也即opcode。
Opcode是PHP程序执行的最基本单位。一个opcode由两个参数(op1,op2)、返回值和处理函数组成。PHP程序最终被翻译为一组opcode处理函数的顺序执行。

常见的几个处理函数：
> ZEND_ASSIGN_SPEC_CV_CV_HANDLER : 变量分配 （$a=$b） 
ZEND_DO_FCALL_BY_NAME_SPEC_HANDLER：函数调用  
ZEND_CONCAT_SPEC_CV_CV_HANDLER：字符串拼接 $a.$b  
ZEND_IS_EQUAL_SPEC_CV_CONST：判断相等 $a=1  
ZEND_IS_EQUAL_SPEC_CV_CONST：判断相等 $a=1  
ZEND_IS_IDENTICAL_SPEC_CV_CONST：判断相等 $a===1



### 核心数据结构-- HashTable  
HashTable是zend的核心数据结构，在PHP里面几乎并用来实现所有常见功能，我们知道的PHP数组即是其典型应用，此外，在zend内部，如函数符号表、全局变量等也都是基于hash table来实现。  

PHP的hash table具有如下特点：  
> 1.支持典型的key->value查询  
  2.可以当做数组使用  
  3.添加、删除节点是O（1）复杂度  
  4.key支持混合类型：同时存在关联数组合索引数组  
  5.Value支持混合类型：array (“string”,2332)  
  6.附加双向链表，支持线性遍历：如foreach  
  
> **散列结构**：Zend的散列结构是典型的hash表模型，通过链表的方式来解决冲突。需要注意的是zend的hash table是一个自增长的数据结构，当hash表数目满了之后，其本身会动态以2倍的方式扩容并重新元素位置。初始大小均为8。另外，在进行key->value快速查找时候，zend本身还做了一些优化，通过空间换时间的方式加快速度。比如在每个元素中都会用一个变量nKeyLength标识key的长度以作快速判定。  
**双向链表**：Zend hash table通过一个链表结构，实现了元素的线性遍历。理论上，做遍历使用单向链表就够了，之所以使用双向链表，主要目的是为了快速删除，避免遍历。Zend hash table是一种复合型的结构，作为数组使用时，即支持常见的关联数组也能够作为顺序索引数字来使用，甚至允许2者的混合。  
**PHP关联数组**：关联数组是典型的hash_table应用。一次查询过程经过如下几步（从代码可以看出，这是一个常见的hash查询过程并增加一些快速判定加速查找。）  
**PHP索引数组**：索引数组就是我们常见的数组，通过下标访问。例如 $arr[0]，Zend HashTable内部进行了归一化处理，对于index类型key同样分配了hash值和nKeyLength(为0)。内部成员变量nNextFreeElement就是当前分配到的最大id，每次push后自动加一。正是这种归一化处理，PHP才能够实现关联和非关联的混合。由于push操作的特殊性，索引key在PHP数组中先后顺序并不是通过下标大小来决定，而是由push的先后决定。例如 $arr[1] = 2; $arr[2] = 3;对于double类型的key，Zend HashTable会将他当做索引key处理  


### －PHP变量  
PHP是一门弱类型语言，本身不严格区分变量的类型。   
PHP在变量申明的时候不需要指定类型。PHP在程序运行期间可能进行变量类型的隐示转换。当然也可以进行显式类型转换。  
PHP变量可以分为简单类型(int、string、bool)、集合类型(array resource object)和常量(const)。以上所有的变量在底层都是同一种结构 zval。  

Zval主要由三部分组成：
> type：指定了变量所述的类型（整数、字符串、数组等）  
refcount&is_ref：用来实现引用计数(后面具体介绍)  
value：核心部分，存储了变量的实际数据,是一个union，也由此实现了弱类型

-- **变量的作用域**  
> 1.局部变量从变量申明开始到函数结束为整个生命周期；  
  2.全局变量存在整个程序中，申明在函数体之外的普通变量都是全局变量，它的生命周期为整个程序线程；

-- **局部变量和全局变量的实现原理**  
> Zend引擎有一个_zend_executor_globals结构,这个结构中有active_symbol_table和symbol_table， active_symbol_table是保存局部变量，是一个指针类型，symbol_table是保存全局变量；  
函数或者对象的方法在被调用时会创建active_symbol_table来保存局部变量，当程序在顶层中使用某个变量时，ZE就会在symbol_table中进行遍历， 同理，如果程序运行于某个函数中，Zend引擎会遍历查询与其对应的active_symbol_table， 而每个函数的active_symbol_table是相对独立的，由此而实现的作用域的独立。
	
-- **变量的引用计数**  
> 1. 通过zval的成员变量is_ref和ref_count实现，通过引用计数，多个变量可以共享同一份数据。避免频繁拷贝带来的大量消耗  
> 2. 变量在进行赋值操作时，zend将变量指向相同的zval同时ref_count++，在unset操作时，对应的ref_count-1。只有ref_count减为0时才会真正执行销毁操作。如果是引用赋值，则zend会修改is_ref为1。
> 3. 通过引用计数实现变量共享数据，那如果改变其中一个变量值呢？当试图写入一个变量时，Zend若发现该变量指向的zval被多个变量共享，则为其复制一份ref_count为1的zval，并递减原zval的refcount，这个过程称为“zval分离”。可见，只有在有写操作发生时zend才进行拷贝操作，因此也叫copy-on-write(写时拷贝)  

### -函数  
-- **函数的数据结构**  
在Zend中分为以下五种类型：
> #define ZEND_INTERNAL_FUNCTION  //系统内部函数  
#define ZEND_USER_FUNCTION        //用户定义函数           
#define ZEND_OVERLOADED_FUNCTION    //重载函数    
#define ZEND_EVAL_CODE              //变量函数       
#define ZEND_OVERLOADED_FUNCTION_TEMPORARY   //重载临时函数

-- **匿名函数和闭包**  
一类不需要指定标示符作为名称的函数，而又可以被调用的函数或子例程，匿名函数可以方便的作为参数传递给其他函数。

#### 使用场景
> 1. 回调函数   
> 2. 函数调用因条件不同而不同的逻辑需求；

--- 使用create_function创建类“匿名”函数  
> string create_function ( string $args , string $code )  传入函数参数，执行相应的函数代码；这种属于类匿名函数,实质上并不是真正意义上的匿名函数。  


### -内存管理  
PHP的内存管理可以被看作是分层（hierarchical）的。 它分为三层：存储层（storage）、堆层（heap）和接口层（emalloc/efree）。 存储层通过 malloc()、mmap() 等函数向系统真正的申请内存，并通过 free() 函数释放所申请的内存。 存储层通常申请的内存块都比较大，这里申请的内存大并不是指storage层结构所需要的内存大， 只是堆层通过调用存储层的分配方法时，其以大块大块的方式申请的内存，存储层的作用是将内存分配的方式对堆层透明化。

--PHP内存管理器的架构图：
![image](http://www.gavin.xin/wp-content/uploads/2017/05/06-02-01-zend-memeory-manager.jpg)

-- PHP内存管理实现方案  
 > 1. 当初始化内存管理时，调用函数是zend_mm_startup,它会初始化storage层的分配方案， 初始化段大小，压缩边界值，并调用zend_mm_startup_ex()初始化堆层。它对应的环境变量名为：ZEND_MM_MEM_TYPE。 这里的初始化的段大小可以通过ZEND_MM_SEG_SIZE设置，如果没设置这个环境变量，程序中默认为256 * 1024。
 > 2. PHP中的内存管理主要工作就是维护三个列表：小块内存列表（free_buckets）、 大块内存列表（large_free_buckets）和剩余内存列表（rest_buckets）。

-- PHP内存分配流程  
![image](http://www.gavin.xin/wp-content/uploads/2017/05/06-03-php-memory-request-free.jpg)  

---ZendMM对内存分配的处理主要有以下步骤：  
> 1. 内存检查。 对要申请的内存大小进行检查，如果太大（超出memory_limit则报 Out of Memory）;  
> 2. 如果命中缓存，使用fastcache得到内存块(详见第五节)，然后直接进行第5步;  
> 3. 在ZendMM管理的heap层存储中搜索合适大小的内存块, 在这一步骤ZendMM通过与ZEND_MM_MAX_SMALL_SIZE进行大小比较， 把内存请求分为两种类型： large和small。small类型的的请求会先使用zend_mm_low_bit函数 在mm_heap中的free_buckets中查找，未找到则使用与large类型相同的方式： 使用zend_mm_search_large_block函数在“大块”内存（_zend_mm_heap->large_free_buckets）中进行查找。 如果还没有可以满足大小需求的内存，最后在rest_buckets中进行查找。 也就是说，内存的分配是在三种列表中小到大进行的。 找到可以使用的block后，进行第5步;  
> 4. 如果经过第3步的查找还没有找到可以使用的资源（请求的内存过大），需要使用ZEND_MM_STORAGE_ALLOC函数向系统再申请一块内存（大小至少为ZEND_MM_SEG_SIZE），然后直接将对齐后的地址分配给本次请求。跳到第6步;  
> 5. 使用zend_mm_remove_from_free_list函数将已经使用block节点在zend_mm_free_block中移除;  
> 6. 内存分配完毕，对zend_mm_heap结构中的各种标识型变量进行维护，包括large_free_buckets， peak，size等;  
> 7. 返回分配的内存地址;  

-- PHP内存释放：
> 当程序unset一个变量或者是其他的释放行为时， ZendMM并不会直接立刻将内存交回给系统，而是只在自身维护的内存池中将其重新标识为可用， 按照内存的大小整理到上面所说的三种列表（small,large,free）之中，以备下次内存申请时使用。   



### - 线程安全  
> 在多线程系统中，进程保留着资源所有权的属性，而多个并发执行流是执行在进程中运行的线程。 如 Apache2 中的 worker，主控制进程生成多个子进程，每个子进程中包含固定的线程数，各个线程独立地处理请求。 同样，为了不在请求到来时再生成线程，MinSpareThreads 和 MaxSpareThreads 设置了最少和最多的空闲线程数； 而 MaxClients 设置了所有子进程中的线程总数。如果现有子进程中的线程总数不能满足负载，控制进程将派生新的子进程。  

-- TSRM的实现  
PHP源码：
```
/* The memory manager table */
static tsrm_tls_entry   **tsrm_tls_table=NULL; //用来存放各个线程的链表
static int              tsrm_tls_table_size; // 用来表示 **tsrm_tls_table 的大小
static ts_rsrc_id       id_count; //作为全局变量资源的 id 生成器，是全局唯一且递增的
 
/* The resource sizes table */
static tsrm_resource_type   *resource_types_table=NULL;  //用来存放全局变量对应的资源
static int                  resource_types_table_size;  //表示 *resource_types_table 的大小  

```
> 【注】当新增一个全局变量时，id_count 会自增1（加上线程互斥锁）。然后根据全局变量需要的内存、构造函数、析构函数生成对应的资源tsrm_resource_type，存入 *resource_types_table，再根据该资源，为每个线程的所有tsrm_tls_entry节点添加其对应的全局变量。  



