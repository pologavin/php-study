<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/1/30
 * Time: 下午6:20
 */

class A 
{
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
        echo 99;
    }

    public function __call($method,$arguments) {
        if(method_exists($this, $method)) {
            echo 22;
            call_user_func_array(array($this,$method),$arguments);
        }
    }

    protected function test()
    {
        echo 88;
    }
}
$a = new A();
$a();
$a->test();
