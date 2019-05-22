<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/3/19
 * Time: 上午10:20
 */

class Test1{

    public function main()
    {
        try{
            throw new Exception('exception!!!');
        }catch (Exception $e) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                echo $errno;
            });
            debug_print_backtrace();
        }
    }
}
//(new Test1())->main();
@call_user_func_array(['Test1', 'main'], []);

echo 555;
