<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/6/13
 * Time: 下午2:02
 */

function makeSign($params, $md5_key)
{
    if (isset($params['_sign'])) {//不能带入这个参数
        return ''; //返回空，默认就是有问题了
    }
    ksort($params);
    $temp = [];
    foreach ($params as $key => $val) {
        $temp[] = $key . (is_array($val) ? json_encode($val) : $val);
    }
    return strtoupper(md5($md5_key . utf8_encode(join('', $temp)) . $md5_key));
}

echo makeSign(json_decode('{"order_id":"136120","log_type":"cancel","op_id":"247","op_user":"godwin.gao","action":"订单取消","remark":"客服中心服务流水号【218】;操作客服人员godwin.gao:操作：订单【136120】订单取消.","operation_log":{"action":"订单取消","remark":"客服中心服务流水号【218】;操作客服人员godwin.gao:操作：订单【136120】订单取消."}}',true), 'CZQMzgJql6Doxf4yFI89qQbGfliLEdXJ');