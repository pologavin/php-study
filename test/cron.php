<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/6/10
 * Time: 下午3:42
 */
// driverId = 1746
$id = 2421;
$type = 3;

while (true) {
    switch ($type) {
        case 1:
            $res = getValidDriverVip($id);
            break;
        case 2:
            $res = getDelayRefundList($id);
            break;
        case 3:
            $res = searchOrdersByPhone($id);
            break;
        case 4:
            $res = getDriverInfo($id);
    }
    if ($res) {
        break;
    }
    $id++;
}
function getValidDriverVip($id)
{
    $url = 'http://dcore2-dev.myhll.cn/index.php?_g=panel&from_svc=ap2&_m=get_valid_driver_vip&args=' . json_encode(['driver_id' => $id], JSON_UNESCAPED_UNICODE);
    $data = curlFileGetContents($url);
    $data = json_decode($data, true);
    if (!empty($data['data']['vip_pkg_item'])) {
        echo $id . PHP_EOL;
        var_dump($data['data']['vip_pkg_item']);
        return true;
    }

    return false;
}

function getDelayRefundList($id)
{
    $url = 'http://ucore2-dev.myhll.cn/index.php?_g=panel&from_svc=csc&_m=get_delay_refund_list&args=' . urlencode(json_encode(['user_id' => $id, 'page_no' => 1, 'page_size' => 50], JSON_UNESCAPED_UNICODE));
    $data = curlFileGetContents($url);
    $data = json_decode($data, true);
    if (!empty($data['data']['data_list'])) {
        echo $id . PHP_EOL;
        var_dump($data['data']['data_list']);
        return true;
    }

    return false;
}

function searchOrdersByPhone($id)
{
    $url = 'http://ucore2-dev.myhll.cn/index.php?_g=panel&from_svc=ap2&_m=search_orders_by_phone&args=' . urlencode(json_encode(['user_id' => $id, 'order_status_arr' => [0,1,7]], JSON_UNESCAPED_UNICODE));
    $data = curlFileGetContents($url);
    $data = json_decode($data, true);
    if (!empty($data['data']['order_detail_by_phone'])) {
        echo $id . PHP_EOL;
        var_dump($data['data']['order_detail_by_phone']);
        return true;
    }

    return false;
}

function getDriverInfo($id)
{
    $url = 'http://dcore2-dev.myhll.cn/index.php?_g=panel&from_svc=csc&_m=get_driver_detail&args=' . urlencode(json_encode(['driver_id' => [$id],], JSON_UNESCAPED_UNICODE));
    $data = curlFileGetContents($url);
    $data = json_decode($data, true);
    if (!empty($data['data']['driver'][0])) {
        if($data['data']['driver'][0]['full_name'] != $data['data']['driver'][0]['name_xing'] . $data['data']['driver'][0]['name_ming']) {
            echo $id . PHP_EOL;
            var_dump($data['data']['driver'][0]);
            return true;
        }
    }

    return false;
}

function curlFileGetContents($durl)
{
    // header传送格式
    $headers = array();
    // 初始化
    $curl = curl_init();
    // 设置url路径
    curl_setopt($curl, CURLOPT_URL, $durl);
    // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    // 添加头信息
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // CURLINFO_HEADER_OUT选项可以拿到请求头信息
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    // 不验证SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    // 执行
    $data = curl_exec($curl);
    // 打印请求头信息
//        echo curl_getinfo($curl, CURLINFO_HEADER_OUT);
    // 关闭连接
    curl_close($curl);
    // 返回数据
    return $data;
}