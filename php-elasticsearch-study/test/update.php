<?php
/**
 *
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/4/29
 * Time: 下午3:35
 */
namespace elasticSearch\test;


use elasticSearch\examples\ElasticSearch;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try{
    $index = "jdbc";
    $type = "jdbc";
    $id = '4858';
    $body = [
        'doc' => [
            'name' => 'hun'
        ],
    ];
    $client = ElasticSearch::getInstance($index, $type);
    $result = $client->update($id, $body);
    var_dump($result);
}catch (\Exception $e) {
    echo $e->getMessage();
}