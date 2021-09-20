<?php
/**
 * redis 存储
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2018/11/13
 * Time: 下午3:59
 */

namespace Lib;

require_once dirname(__DIR__) . '/Lib/InstanceTrait.php';

class RedisStore
{

    use InstanceTrait;

    /**
     * @var \Redis
     */
    protected $redis;
    protected $config = [
        'host' => 'redis',
        'port' => 6379,
    ];

    /**
     * RedisStore constructor.
     * @throws \Exception
     */
    public function __construct()
    {

        if (empty($this->config)) {
            return null;
        }

        if (empty($this->redis)) {
            $this->redis = new \Redis();
            try {
                $connect = $this->redis->pconnect($this->config['host'], $this->config['port']);
                if (!$connect) {
                    throw new \Exception("redis connect fail.");
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

    }

    public function add($key, $data)
    {
        return $this->redis->rPush($key, json_encode($data));
    }

    public function getList($key, $start = 0, $end = -1)
    {
        $data = $this->redis->lRange($key, $start, $end);
        if (empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as &$value) {
            $value = json_decode($value, true);
            //$result = array_merge_recursive($result, $value);
            $this->mergeKey($result, $value);
        }
        return $result;
    }

    public function getCount($key)
    {
        $size = $this->redis->lSize($key);

        return empty($size) ? 0 : $size;
    }

    private function mergeKey(&$result, $value)
    {
        $map = &$result;
        foreach ($value as $key => $v) {
            if (!isset($map[$key])) {
                $map[$key] = $v;
                break;
            }
            $map[$key] = &array_merge($map[$key], $v);
        }
    }

}