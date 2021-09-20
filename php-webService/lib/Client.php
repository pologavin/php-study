<?php
/**
 * client
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/12/6
 * Time: 下午1:59
 */

namespace webService\lib;

class Client
{

    private $mode = 'wsdl';

    private $trace = true;  // 开启调试

    private $soapVersion = SOAP_1_2;  // SOAP 版本

    private $encoding = 'UTF-8'; // 编码

    private $compression = 0;

    private $options = array();

    private $serverIP = '127.0.0.1';

    private $serverPort = '80';

    private $serverDir = '';

    private $indexFile = 'server.php';

    private $serviceUri = '';

    private $serviceName = '';

    private $wsdlCacheEnabled = 0; // WSDL 缓存：1开启，0关闭

    public function __construct($params = array())
    {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        $this->options = array(
            'trace' => $this->trace,
            'soap_version' => $this->soapVersion,
            'encoding' => $this->encoding,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        );

        if (!$this->wsdlCacheEnabled) {
            $this->options['cache_wsdl'] = WSDL_CACHE_NONE;
        }

        if ($this->serviceUri == '') {
            $this->serviceUri = 'http://' . $this->serverIP . ':' . $this->serverPort . DIRECTORY_SEPARATOR;
            if(!empty($this->serverDir)) {
                $this->serviceUri .= $this->serverDir . DIRECTORY_SEPARATOR;
            }
            $this->serviceUri .= $this->indexFile . DIRECTORY_SEPARATOR . $this->serviceName;
        }

        if ($this->mode == 'wsdl') {
            $this->serviceUri .= '?wsdl';
        } else {
            $this->options['uri'] = 'http://' . $_SERVER['SERVER_NAME']; // non-WSDL 模式参数
            $this->options['location'] = $this->serviceUri;    // non-WSDL 模式参数，server 端具体路径
            $this->serviceUri = null;
        }

    }

    public function getClient()
    {
        try{
            return new \SoapClient($this->serviceUri, $this->options);
        }catch (\SoapFault $e) {
            echo $e->getMessage();
        }
    }

    public function getServiceUri()
    {
        return $this->serviceUri;
    }
}

