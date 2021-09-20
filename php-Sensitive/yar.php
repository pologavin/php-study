<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/11/16
 * Time: 下午5:13
 */

require dirname(__FILE__) . '/Lib/Sensitive.php';

register_shutdown_function("error_handler");

$service = new Yar_Server(new \Lib\Sensitive());
$service->handle();

function error_handler(){
    file_put_contents('err.log',11, FILE_APPEND);
    file_put_contents('err.log',var_export(error_get_last(), true), FILE_APPEND);
}