<?php
/**
 *
 * User: gaojun<hsbodegj@gmail.com>
 * Date: 2019/3/26
 * Time: 下午2:59
 */

echo htmlspecialchars_decode('加入后&gt;=7天未联系， 我懂不');die();
setcookie('a', 'a', time() + 60*60);
var_dump($_COOKIE);die();

error_reporting(E_NOTICE);

error_log('一个警告！！！', 1, 'hsbodegj@gmail.com');