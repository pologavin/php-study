<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/18
 * Time: 下午7:42
 */

namespace elasticSearch;

require_once ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
register_shutdown_function(function (){
    if(!empty(error_get_last())) {
        file_put_contents(ROOT . DIRECTORY_SEPARATOR . 'logs/err.log', var_export(error_get_last(), true), FILE_APPEND);
    }
});
spl_autoload_register(function ($className) {
    $prefix = __NAMESPACE__ . '\\';
    if (0 === strpos($className, $prefix)) {
        $parts = explode('\\', substr($className, strlen($prefix)));
        $filePath = ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';
        if (is_file($filePath)) {
            require_once $filePath;
        }
    }
});

