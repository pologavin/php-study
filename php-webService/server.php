<?php
/**
 * soap server 入口文件
 * test: php-webService/server.php/test?wsdl
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/6
 * Time: 上午11:31
 */

define('ROOT', dirname(__FILE__));
define('DEFAULT_CLASS' , 'test');

register_shutdown_function("error_handler");

require_once 'autoload.php';

$mode = '';
if (isset($_GET['wsdl'])) {
    $mode = 'wsdl';
}
$classMaps = [
    'test' => \webService\server\Test::class
];
$classKey = getClassKey();

try {
    if (empty($classMaps[$classKey])) {
        throw new Exception("class is illegal！");
    }

    $service = new \webService\lib\Server($mode, $classMaps[$classKey]);
    $service->run();
} catch (Exception $e) {
    echo $e->getMessage();
}

function error_handler()
{
    file_put_contents(ROOT . '/logs/err.log', var_export(error_get_last(), true), FILE_APPEND);
}

function getClassKey()
{
    $args = explode(basename(__FILE__) . DIRECTORY_SEPARATOR, $_SERVER['PHP_SELF']);
    return empty(end($args)) ? DEFAULT_CLASS : end($args);
}