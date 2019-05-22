<?php
/**
 * Created by PhpStorm.
 * User: gaojun
 * Date: 2019/5/17
 * Time: 下午5:27
 */
namespace async_study\http\lib;

interface TaskInterface
{
    /**
     * get created curl resource
     * @return resource curl
     */
    public function getCurl();
    /**
     * create curl resource
     * @return mixed
     */
    public function createCurl();
}