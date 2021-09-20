<?php
/**
 * 测试入口
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/11/13
 * Time: 下午4:49
 */
register_shutdown_function("error_handler");

require dirname(__FILE__) . '/Lib/Sensitive.php';

$action = $_REQUEST['a'];
$data = $_REQUEST['data'];

switch ($action) {
    case 'add' :
        add($data);
        break;
    case 'check' :
        check($data);
        break;
}

// 敏感词添加
function add($data)
{
    //$data = json_decode($data, true);
    $data = explode(',', $data);
    if (empty($data)) {
        throw new Exception("data is empty.");
        return false;
    }

    try {
        $sensitive = new \Lib\Sensitive();
        $res = $sensitive->addWords($data);
        if(!$res) {
            echo 'add fail.';
        }else {
            echo 'add success.';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

}

// 敏感词检测
function check($data) {
    if(empty($data)) {
        throw new Exception("data is empty");
        return;
    }

    try{
        $sensitive = new \Lib\Sensitive();
        $result = $sensitive->search($data);
        var_dump($result);
    }catch (Exception $e) {
        echo $e->getMessage();
    }
}
function error_handler(){
    file_put_contents('err.log',11, FILE_APPEND);
    file_put_contents('err.log',var_export(error_get_last(), true), FILE_APPEND);
}