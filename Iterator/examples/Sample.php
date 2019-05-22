<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/4
 * Time: 上午10:22
 */
namespace Iterator;


class Sample implements \Iterator
{
    private $_items;

    public function __construct(&$data)
    {
        $this->_items = $data;
    }

    public function current()
    {
        // TODO: Implement current() method.
        return current($this->_items);
    }

    public function next()
    {
        // TODO: Implement next() method.
        next($this->_items);
    }

    public function key()
    {
        // TODO: Implement key() method.
        return key($this->_items);
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
        reset($this->_items);
    }

    public function valid()
    {
        // TODO: Implement valid() method.
        return ($this->current() != false);
    }
}

$data = range(1, 100);
if(!$data instanceof \Traversable) {
    echo 'NOT Traversable!' . "\r\n";
}
if(!is_iterable($data)) {
    echo 'NOT Traversable!' . "\r\n";
}
var_dump($data);

$sample = new Sample($data);

while ($sample->valid()) {
    echo $sample->current() . "\r\n";
    $sample->next();
}