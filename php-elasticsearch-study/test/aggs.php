<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/4/19
 * Time: 下午4:54
 */

namespace elasticSearch\test;

use elasticSearch\examples\ElasticSearch;

define("ROOT", dirname(__DIR__));
require_once ROOT . DIRECTORY_SEPARATOR . "init.php";

try {
    $index = "jdbc";
    $type = "jdbc";
    $client = ElasticSearch::getInstance($index, $type);
    // 对text字段的聚合，映射为keyword不进行分词
    $aggregations = [
        'user_count' => [
            'terms' => [
                'field' => 'uname.keyword',
            ],
        ],
    ];
    $result = $client->aggregations($aggregations);
    var_dump($result);
} catch (\Exception $e) {
    echo $e->getMessage();
}