<?php
/**
 *
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/1/3
 * Time: 下午4:43
 */
class class1
{
    public function func1()
    {
        echo 22;
        return;
    }
    
    public function func2()
    {
        echo 33;
        return;
    }
}

$obj = new class1();
$obj->func1();
$obj->func2();
