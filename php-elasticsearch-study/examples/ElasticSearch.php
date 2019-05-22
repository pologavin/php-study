<?php
/**
 * elasticSearch 基本操作
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/1/29
 * Time: 下午5:13
 */

namespace elasticSearch\examples;

use Elasticsearch\ClientBuilder;
use elasticSearch\conf\Es;
use elasticSearch\InstanceTrait;

class ElasticSearch
{
    /**
     * @var \Elasticsearch\Client|string
     */
    protected $connect = '';
    protected $index = [];

    use InstanceTrait;

    public function __construct($index, $type)
    {
        $uri = $this->_getEsUri(Es::ES_HOST, Es::ES_PORT);
        $this->connect = ClientBuilder::create()->setHosts([$uri])->build();
        $this->index['index'] = $index;
        $this->index['type'] = $type;
    }

    /**
     * search by map
     * @param array $map
     * @return array
     */
    public function search(array $map = [])
    {
        $map = array_merge($this->index, $map);
        $data = $this->connect->search($map);
        if (empty($data['hits'])) {
            return [];
        }
        return [
            'data' => array_column($data['hits']['hits'], "_source"),
            'total' => $data['hits']['total']
        ];
    }

    public function count(array $map = [])
    {
        $map = array_merge($this->index, $map);
        $data = $this->connect->count($map);
        return empty($data['count']) ? 0 : $data['count'];
    }

    /**
     * aggregations by map
     * @param array $aggregations
     * @param array $map
     * @return mixed
     */
    public function aggregations( array $aggregations, array $map = [])
    {

        $map = array_merge($this->index, $map);
        $map['body']['aggs'] = $aggregations;
        $data = $this->connect->search($map);

        $result = [];
        array_walk($aggregations, function ($v, $k) use ($data, &$result){
            $result[$k] = $data['aggregations'][$k]['buckets'];
        });
        return $result;
    }

    /**
     * search by scroll by map
     * @param array $map
     * @param array $slice
     * @return array
     */
    public function searchByScroll(array $map = [], array $slice = [])
    {
        $map = array_merge($this->index, $map);
        $map['search_type'] = 'dfs_query_then_fetch';
        $map['scroll'] = isset($map['scroll']) ? $map['scroll'] : Es::DEFAULT_SCROLL_TLL;
        $map['size'] = isset($map['size']) ? $map['size'] : Es::DEFAULT_SIZE;

        if(!empty($slice)) {
            $map['body']['slice'] = [
                'id' => $slice['id'] ?? Es::DEFAULT_SLICE_id,
                'max' => $slice['max'] ?? Es::DEFAULT_SLICE_MAX,
            ];
        }

        if (empty($map['scroll_id'])) {
            $data = $this->connect->search($map);
        } else {
            try {
                $data = $this->connect->scroll([
                    'scroll_id' => $map['scroll_id'],
                    'scroll' => $map['scroll']
                ]);
            } catch (\Exception $e) {
                if ($e->getCode() == 404) {
                    $this->connect->clearScroll([
                        'scroll_id' => $map['scroll_id'],
                        'client' => array(
                            'ignore' => 404
                        )
                    ]);
                    unset($map['scroll_id']);
                    $data = $this->connect->search($map);
                }
            }
        }
        if (empty($data['hits'])) {
            if (!empty($map['scroll_id'])) {
                $this->connect->clearScroll([
                    'scroll_id' => $map['scroll_id'],
                    'client' => array(
                        'ignore' => 404
                    )
                ]);
            }
            return [];
        }
        return [
            'data' => array_column($data['hits']['hits'], "_source"),
            'total' => $data['hits']['total'],
            'scroll_id' => $data['_scroll_id']
        ];
    }

    /**
     * index by id
     * @param $id
     * @return array
     */
    public function index($id)
    {
        if (empty($id)) {
            return [];
        }
        $map = array_merge($this->index, ['id' => strval($id), 'body' => []]);
        $data = $this->connect->index($map);
        if (empty($data['hits'])) {
            return [];
        }
        return array_column($data['hits']['hits'], "_source");
    }

    /**
     * @param $id
     * @return array
     */
    public function get($id)
    {
        if (empty($id)) {
            return [];
        }
        $map = array_merge($this->index, ['id' => strval($id)]);
        $data = $this->connect->get($map);
        if (empty($data['_source'])) {
            return [];
        }
        return $data['_source'];
    }

    /**
     * @param $id
     * @param array $data
     * @return array|bool
     */
    public function update($id, array $data)
    {
        if(empty($id)) {
            return false;
        }

        $map = array_merge($this->index, ['id' => $id, 'body' => $data]);
        $result = $this->connect->update($map);
        return $result;
    }

    /**
     * @param $host
     * @param $port
     * @return string
     */
    private function _getEsUri($host, $port)
    {
        $uri = 'http://';
        $uri .= $host . ':' . $port;
        return $uri;
    }
}