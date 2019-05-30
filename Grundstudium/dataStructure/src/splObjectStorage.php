<?php
/**
 * @link https://www.php.net/manual/zh/class.splobjectstorage.php
 * 对象映射
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/28
 * Time: 上午11:54
 */

$objects = new SplObjectStorage();

class A
{

}

class B
{

}

class C
{

}

$objects->attach(new A());
$objects->attach(new B());
$objects->attach(new C());

foreach ($objects as $object) {
    var_dump($object);
}