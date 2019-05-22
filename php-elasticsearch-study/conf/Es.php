<?php
/**
 * es 配置
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/1/29
 * Time: 下午3:58
 */
namespace elasticSearch\conf;

class Es
{
    const ES_HOST = 'es01';
    const ES_PORT = 9200;

    const DEFAULT_SIZE = 50;
    const DEFAULT_SCROLL_TLL = '1m';
    const DEFAULT_SLICE_id = 0;
    const DEFAULT_SLICE_MAX = 1;

}