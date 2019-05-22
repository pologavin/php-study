<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/1/29
 * Time: 下午4:44
 */
namespace elasticSearch\test;


use elasticSearch\examples\ElasticSearch;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try{
    $index = "jdbc";
    $type = "jdbc";
    $id = '4858';
    $client = ElasticSearch::getInstance($index, $type);
    $result = $client->get($id);
    var_dump($result);
}catch (\Exception $e) {
    echo $e->getMessage();
}