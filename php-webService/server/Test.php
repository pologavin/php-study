<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/6
 * Time: 上午11:51
 */
namespace webService\server;

class Test
{
    public function __construct()
    {

    }

    public function func1($value = 1)
    {
        $data = [
            'uid' => intval($value),
            'name' => 'gavin',
            'age' => 23,
            'create_time' => time()
        ];
        file_put_contents(ROOT . '/logs/debug.log', var_export($data, true), FILE_APPEND);
        return ['data' => $data];
    }
}