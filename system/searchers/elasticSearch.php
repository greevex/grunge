<?php
namespace grunge\system\searchers;

/**
 * Description of searcher
 *
 * @author GreeveX <greevex@gmail.com>
 */
class elasticSearch
implements \grunge\system\interfaces\searcher {

    private $backend;
    private $config;

    public function __construct($configName = 'default') {
        \grunge\system\service\fileLoader::
                load(GRUNGE_PATH . '/libs/elasticsearch/ElasticSearchClient.php');
        $this->config = \grunge\system\systemConfig::$searcher[$configName];
        $transport_class = "\ElasticSearchTransport{$this->config['transport']}";
        $transport = new $transport_class(
                $this->config['host'],
                $this->config['port']);
        $this->backend = new \ElasticSearchClient($transport,
                $this->config['index_name'],
                $this->config['type_name']);
    }

    public function getBackend()
    {
        return $this->backend;
    }

    public function indexThis($document, $uniq_id = false)
    {
        return $this->backend->index($document, $uniq_id);
    }

    public function search($query, $offset = 0, $limit = 10, $search_type = 'text', $glue = 'OR')
    {
        $query = explode(':', $query);
        $field = array_shift($query);
        $query = implode(':', $query);
        return $this->searchByField($field, $query, $offset, $limit, $search_type);
    }

    public function searchByField($field, $query, $offset = 0, $limit = 10,
            $search_type = 'text', $glue = 'OR', $sort = false, $highlight = false)
    {
        try {
            $hlight = false;
            if($highlight) {
                $hlight = array(
                    "number_of_fragments" => 3,
                    "fragment_size" => 1024,
                    "fields" => array()
                );
                foreach($highlight as $hfield => $htags) {
                    $hlight['fields'][] = array(
                        $hfield => array(
                            "pre_tags" => array($htags[0]),
                            "post_tags" => array($htags[1])
                        )
                    );
                }
            }
            if(!$sort) {
                $sort = array(
                    '_score' => array(
                        'order' => 'desc'
                    )
                );
            }
            $query = array(
                'query' => array(
                    $search_type => array(
                        $field => $query
                    )
                ),
                'from' => $offset,
                'size' => $limit,
                'sort' => $sort,
                'highlight' => $hlight
            );
            try {
                $result = $this->backend->search($query);
            } catch(Exception $e) {
                $result = false;
            }
            return $result;
        } catch(Exception $e) {
            return false;
        }
    }

    public function searchByParams($params)
    {
        try {
            try {
                $result = $this->backend->search($params);
            } catch(Exception $e) {
                $result = false;
            }
            return $result;
        } catch(Exception $e) {
            return false;
        }
    }

    public function customRequest($path, $method = 'GET', $params = false, $verbose = true)
    {
        $this->backend->setIndex("");
        $this->backend->setType("");
        $data = $this->backend->request($path, $method, $params, $verbose);
        $this->backend->setIndex($this->config['index_name']);
        $this->backend->setType($this->config['type_name']);
        return $data;
    }

    public function getStats()
    {
        return $this->customRequest('_count', 'GET', false, true);
    }

    public function getConfig($key = null)
    {
        return $key == null ? $this->config : $this->config[$key];
    }
}