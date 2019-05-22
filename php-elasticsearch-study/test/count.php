<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/4/28
 * Time: 上午11:53
 */

namespace elasticSearch\test;

use elasticSearch\examples\ElasticSearch;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try {
    $index = "jdbc";
    $type = "jdbc";
    $client = ElasticSearch::getInstance($index, $type);
    $map = [
        "body" => [
            "query" => [
                "match" => [
                    "uname" => "9"
                ],
            ],
        ],
    ];
    //$result = $client->search($map);
    $result = $client->count($map);
    var_dump($result);
} catch (\Exception $e) {
    echo $e->getMessage();
}