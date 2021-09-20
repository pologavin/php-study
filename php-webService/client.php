<?php
/**
 * client
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/6
 * Time: 下午5:13
 */

define('ROOT', dirname(__FILE__));

register_shutdown_function("error_handler");

require_once 'autoload.php';

$mode = 'non-wsdl'; // or non-wsdl
$params = array(
    'serverIP' => 'wbs-dev.huolala.cn',
    'serverPort' => '11067',
    'mode' => $mode,
    'serviceName' => 'test'
);

try {
    $clientClass = new \webService\lib\Client($params);
    $client = $clientClass->getClient();
    $result = $client->__soapCall('func1', [12]);
    var_dump($result);die();
} catch (Exception $e) {
    echo $e->getMessage();
}

function error_handler()
{
    file_put_contents(ROOT . '/logs/err.log', var_export(error_get_last(), true), FILE_APPEND);
}