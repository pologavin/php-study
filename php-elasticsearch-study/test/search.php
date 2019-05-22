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
    $client = ElasticSearch::getInstance($index, $type);
    $map = [
        "body" => [
            "query" => [
                "match" => [
                    "uname" => "9"
                ],
            ],
        ],
        "sort" => ["ucreatetime:desc"],
        "size" => 8,
        "from" => 0,
        'client' => [
            'future' => 'lazy'
        ],
        'scroll_id' => 'DnF1ZXJ5VGhlbkZldGNoBQAAAAAAACRPFnhiOG9tOEdpVFYtVmVYeVJQdFRnUEEAAAAAAAAkUBZ4YjhvbThHaVRWLVZlWHlSUHRUZ1BBAAAAAAAAJFEWeGI4b204R2lUVi1WZVh5UlB0VGdQQQAAAAAAACRSFnhiOG9tOEdpVFYtVmVYeVJQdFRnUEEAAAAAAAAkUxZ4YjhvbThHaVRWLVZlWHlSUHRUZ1BB'
    ];
    //$result = $client->search($map);
    $result = $client->searchByScroll($map, [
        'id' => 1,
        'max' => 4
    ]);
    var_dump($result);
}catch (\Exception $e) {
    echo $e->getMessage();
}