**php7 内存管理学习**

### 自动GC机制
1. 引用计数
> 用来记录当前有多少zval指向同一个zend_value。每个zend_value结构都定义了一个zend_refcounted结构的变量计数器，当有新的zval指向这个value时，计数器加1；当zval销毁时，计数器减1。只有当计数等于0是，内存会对value进行释放。

zend_refcounted结构
```
typedef struct _zend_refcounted_h {
    // 引用计数器
	uint32_t         refcount;			/* reference counter 32-bit */
	union {
		struct {
			ZEND_ENDIAN_LOHI_3(
			    // 类型
				zend_uchar    type,
				zend_uchar    flags,    /* used for strings & objects */
				// 垃圾回收时用到
				uint16_t      gc_info)  /* keeps GC root number (or 0) and color */
		} v;
		uint32_t type_info;
	} u;
} zend_refcounted_h;

struct _zend_refcounted {
	zend_refcounted_h gc;
};
```

2. 写时复制
> 当多个变量zval指向同一个zend_value时，其中一个变量的value发生修改，导致原本指向同一个zend_value变成不同的zend_value，这时就会对zend_value进行分离。这个分离的过程其实是先发生复制在进行分离，复制出来的zend_value进行修改后断开原来的zend指向。  

实际上只有string和array两种类型支持value的分离。在这个分离过程中如果发现有变量的refcount=0就会自动释放value，这个就是自动GC机制。否则通过unset（）进行自动销毁变量进行回收。



- 垃圾回收机制
> 在array和object这两种类型中存在一个循环引用的场景，即变量内部引用变量本身，这样引用计数有一个是来自自身成员，当所有外部引用全部断开时，数组的recount依然大于0而得不到释放。所以就设计一个垃圾回收器来处理这种场景。

1. 垃圾回收器
>   1. 垃圾收集：  
        zval.u1.type_flag的类型掩码标识“IS_TYPE_COLLECTABLE”的变量会被垃圾回收器采集到一个buffer缓存区中。采集时机是refcount减少时，同一个变量不会重复收集。
>   2. 垃圾回收：  
        等到垃圾回收器收集到的垃圾达到一定数量后，就会启动垃圾鉴定，回收程序。
>   3. 回收算法：  
        由于垃圾是由于成员自身导致的，那么回收时遍历value的所有成员，将所有成员的引用计数减1，zend_refcounted_h.u.gc_info置为GC_GREY，再次遍历buffer,检查value的引用是否为0，为0则zend_refcounted_h.u.gc_info置为GC_WHITE,如果不为0则对value进行recount加1还原操作。最后将GC_WHITE的value垃圾释放，同时也删除非GC_WHITEvalue。

垃圾回收器zend_gc_globals结构

```
typedef struct _zend_gc_globals {
    // 是否启用gc
	zend_bool         gc_enabled;
	// 是否在垃圾检查过程中
	zend_bool         gc_active;
	// 缓存区是否满
	zend_bool         gc_full;

    // 启动时分配的用于保存可能垃圾的缓存区
	gc_root_buffer   *buf;				/* preallocated arrays of buffers   */
	// 指向buf中最新加入的一个可能垃圾
	gc_root_buffer    roots;			/* list of possible roots of cycles */
	// 指向buf中没有使用的buffer
	gc_root_buffer   *unused;			/* list of unused buffers           */
	//  指向buf中第一个没有使用的buffer
	gc_root_buffer   *first_unused;		/* pointer to first unused buffer   */
	//  指向buf尾部
	gc_root_buffer   *last_unused;		/* pointer to last unused buffer    */

    // 待释放的垃圾
	gc_root_buffer    to_free;			/* list to free                     */
	gc_root_buffer   *next_to_free;

    // 统计gc运行的次数
	uint32_t gc_runs;
	// 统计已回收的垃圾数
	uint32_t collected;

#if GC_BENCH
	uint32_t root_buf_length;
	uint32_t root_buf_peak;
	uint32_t zval_possible_root;
	uint32_t zval_buffered;
	uint32_t zval_remove_from_buffer;
	uint32_t zval_marked_grey;
#endif

	gc_additional_buffer *additional_buffer;

} zend_gc_globals;
```


- 内存池
> ZendMM(Zend Memery Manager)是一套类似tcmalloc的内存管理的内存池技术，
1. 三种粒度的内存块
>   1. chunk:大小为2MB，申请大于2MB的内存，直接调用系统分配。都是以chunk为单位,chunk是内存池向系统申请，释放内存的最小粒子。
>   2. page:大小为4KB,申请内存大于3072B(page大小的3/4)，小于2044KB(即511个page大小)，分配若干个page。
>   3. slot:内存池在不同page上分配定义好的三十种同等大小（8，16，24....3072B），申请内存小于等于3072B(page大小的3/4)直接从对应page上查找可用的slot。

2. 内存池存储结构zend_mm_heap,这个结构通过全局变量alloc_globals保存。AG宏操作就是这个变量。

```
struct _zend_mm_heap {
#if ZEND_MM_CUSTOM
	int                use_custom_heap;
#endif
#if ZEND_MM_STORAGE
	zend_mm_storage   *storage;
#endif
#if ZEND_MM_STAT
	size_t             size;                    /* current memory usage */
	size_t             peak;                    /* peak memory usage */
#endif
    // 小内存分配的可用位置链表slot，ZEND_MM_BINS等于30，
	zend_mm_free_slot *free_slot[ZEND_MM_BINS]; /* free lists for small sizes */
#if ZEND_MM_STAT || ZEND_MM_LIMIT
	size_t             real_size;               /* current size of allocated pages */
#endif
#if ZEND_MM_STAT
	size_t             real_peak;               /* peak size of allocated pages */
#endif
#if ZEND_MM_LIMIT
	size_t             limit;                   /* memory limit */
	int                overflow;                /* memory overflow flag */
#endif
    // 大内存链表
	zend_mm_huge_list *huge_list;               /* list of huge allocated blocks */
    // 指向chunk链表的头部
	zend_mm_chunk     *main_chunk;
	// 缓存的chunk链表
	zend_mm_chunk     *cached_chunks;			/* list of unused chunks */
	// 已分配chunk数
	int                chunks_count;			/* number of alocated chunks */
    // 当前request使用的chunk峰值
	int                peak_chunks_count;		/* peak number of allocated chunks for current request */
	// 缓存的chunk数
	int                cached_chunks_count;		/* number of cached chunks */
	double             avg_chunks_count;		/* average number of chunks allocated per request */
	int                last_chunks_delete_boundary; /* numer of chunks after last deletion */
	int                last_chunks_delete_count;    /* number of deletion over the last boundary */
#if ZEND_MM_CUSTOM
	union {
		struct {
			void      *(*_malloc)(size_t);
			void       (*_free)(void*);
			void      *(*_realloc)(void*, size_t);
		} std;
		struct {
			void      *(*_malloc)(size_t ZEND_FILE_LINE_DC ZEND_FILE_LINE_ORIG_DC);
			void       (*_free)(void*  ZEND_FILE_LINE_DC ZEND_FILE_LINE_ORIG_DC);
			void      *(*_realloc)(void*, size_t  ZEND_FILE_LINE_DC ZEND_FILE_LINE_ORIG_DC);
		} debug;
	} custom_heap;
#endif
};
```
3. 内存池的三层架构
>   1. storage: 存储层，是通过malloc(),mmap()等函数真正向系统申请内存和通过free()函数释放内存。存储层申请的内存块是大块的，共有4种内存分配方案: malloc，win32，mmap_anon，mmap_zero，默认使用malloc分配内存；
>   2. heap:堆层，对存储层申请的内存进行拆分，结构化的不同规格的小的内存块，为接口层提供不同大小内存块。
>   3. emalloc/efree: 接口层，从heap中查找合适大小的内存块进行分配和释放内存块。

4. 内存分配
> 内存池有三种不同粒度的内存块，因此在内存分配时会按照内存的大小自动选择哪种内存进行分配。
>   1. Huge分配：超过2M大小内存的分配，分配时对齐到n个chunk。内存分配对齐是先按照实际要申请的内存大小申请一次，如果恰好是2M的整数倍，就直接返回使用，否则释放该内存按照“申请内存大小+2M”重新申请一块。多申请的2M内存进行调整，从系统分配地址向后偏移到最近的2M的整数倍位置，调整后的多余内存再次被释放掉。
>   2. large分配：大于3072B小于2044KB内存的分配，单个chunk内存块会分成512个page块，即每个page块大小为4KB。根据申请内存大小计算出需要分配的page个数，然后按照page进行分配。
>   3. small分配: 首先检查申请规格的内存是否已经分配，如果没有或者分配用完，则申请相对应页数的page。不同规格的slot会根据申请内存大小返回不同规格的slot内存块。

5. 内存释放
> 主要通过efree()来完成，根据内存地址自动判断那种粒度内存进行不同的释放逻辑，内存释放只能通过内存地址进行。


### 引用计数系统的同步周期回收
> 由于引用计数算法存在无法回收循环应用导致的内存泄露问题，在 PHP 5.3 之后对内存回收的实现做了优化，通过采用 引用计数系统的同步周期回收 算法实现内存管理。引用计数系统的同步周期回收算法是一个改良版本的引用计数算法。

1. 它在引用基础上做出了如下几个方面的增强：
    1. 引入了可能根（possible root）的概念：通过引用计数相关学习，我们知道如果一个变量（zval）被引用，要么是被全局符号表中的符号引用（即变量），要么被复杂类型（如数组）的 zval 中的符号（数组的元素）引用，那么这个 zval 变量容器就是「可能根」。
    2. 引入根缓冲区（root buffer）的概念：根缓冲区用于存放所有「可能根」，它是固定大小的，默认可存 10000 个可能根，如需修改可以通过修改 PHP 源码文件 Zend/zend_gc.c 中的常量 GC_ROOT_BUFFER_MAX_ENTRIES，再重新编译。
    3. 回收周期：当缓冲区满时，对缓冲区中的所有可能根进行垃圾回收处理。
    
2. 新的回收算法执行过程如下图：
![image](https://www.php.net/manual/zh/images/12f37b1c6963c1c5c18f30495416a197-gc-algorithm.png)

3. 回收过程：
    1. 缓冲区（紫色框部分，称为疑似垃圾），存储所有可能根（步骤 A）；
    2. 采用深度优先算法遍历「根缓冲区」中所有的「可能根（即 zval 遍历容器）」，并对每个 zval 的 refcount 减 1，为了避免遍历时对同一个 zval 多次减 1（因为不同的根可能遍历到同一个 zval）将这个 zvel 标记为「已减」（步骤 B）；
    3. 再次采用深度优先遍历算法遍历「可能根 zval」。当 zval 的 refcount 值不为 0 时，对其加 1, 否则保持为 0。并请已遍历的 zval 变量容器标记为「已恢复」（即步骤 B 的逆运算）。那些 zval 的 refcount 值为 0 （蓝色框标记）的就是应该被回收的变量（步骤 C）；
    4. 删除所有 refcount 为 0 的可能根（步骤 D）。

4. 优化回收算法优势：
    1. 将内存泄露控制在阀值内，这个由缓存区实现，达到缓冲区大小执行新一轮垃圾回收；
    2. 提升了垃圾回收性能，不是每次 refcount 减 1 都执行回收处理，而是等到根缓冲区满时才开始执行垃圾回收。

> 具体可以参考[php手册的回收周期]https://php.net/manual/zh/features.gc.collecting-cycles.php) 


### php7 和php5 的内存管理差别
1. php5 问题：  
    1. zval 总是单独 从堆中分配内存；
    2. zval 总是存储引用计数和循环回收 的信息，即使是整型（bool /null）这种可能并不需要此类信息的数据；
    3. 在使用对象或者资源时，直接引用会导致两次计数；
    4. 某些间接访问需要一个更好的处理方式。比如现在访问存储在变量中的对象间接使用了四个指针（指针链的长度为四）；
    5. 直接计数也就意味着数值只能在 zval 之间共享。如果想在 zval 和 hashtable key 之间共享一个字符串就不行（除非 hashtable key 也是 zval）。

2. php7 的调整：
    1. zval 需要的内存不再是单独从堆上分配，不再由 zval 存储引用计数。
    2. 复杂数据类型（比如字符串、数组和对象）的引用计数由其自身来存储。
    3. 数字简单类型值不需要单独分配内存，也不使用引用计数。复杂类型（字符串,数组和对象）单独分配内存和自身存储引用计数。
    4. 初始化的字符串也是不使用引用计数，只有在计算中才会存储引用计数。
    5. 不会再有两次计数的情况。在对象中，只有对象自身存储的计数是有效的。
    6. 由于现在计数由数值自身存储（PHP 有 zval 变量容器存储），所以也就可以和非 zval 结构的数据共享，比如 zval 和 hashtable key 之间。
    7. 间接访问需要的指针数减少了。