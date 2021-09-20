<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/8/15
 * Time: 上午11:54
 */
$data = read_csv("clear_cid.csv");
$newData = [];
array_walk($data, function ($v) use (&$newData) {
    if (!empty($v[2])) {
        $newData[$v[0]][$v[2]][] = $v[1];
    }
});
foreach ($newData as $k => $v) {
    $phones = [];
    array_walk_recursive($v, function ($v) use(&$phones){
        $phones[] = $v;
    });
    $phones = array_count_values($phones);
    var_dump(max($phones));die();
}


function read_csv($csv_file = '', $lines = 0, $offset = 0)
{
    setlocale(LC_ALL, 'zh_CN');

    if (empty($csv_file) || !@fopen($csv_file, "r")) {
        return [];
    }

    $file = @fopen($csv_file, "r");

    $i = $j = 0;
    //跳到起始行
    while (false !== ($line = fgets($file))) {
        if ($i++ < $offset) {
            continue;
        }
        break;
    }


    //不设置读取行数时，读取全部。
    while (($lines == 0 || $j++ < $lines) && !feof($file)) {
        $data[] = fgetcsv($file);
    }
    //$data = eval('return ' . iconv('gbk', 'utf-8', var_export($data, true)) . ';');
    foreach ($data as $key => $value) {

        if (!$value) {
            unset($data[$key]);
        }
    }
    fclose($file);
    return $data;
}