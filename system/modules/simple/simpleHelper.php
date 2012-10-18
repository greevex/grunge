<?php
namespace grunge\system\modules\simple;

class simpleHelper {

    /**
     *
     * @var \grunge\system\systemToolkit
     */
    protected $toolkit;

    /**
     *
     * @var \grunge\system\io\response
     */
    protected $output;

    /**
     *
     * @var \grunge\system\io\request
     */
    protected $request;

    /**
     * @var \grunge\system\renders\view
     */
    protected $view;

    /**
     * @var \grunge\system\cache\cache
     */
    protected $cache;

    /**
     * @var \grunge\system\orm\mongoDb
     */
    protected $mongo;

    /**
     * @var \grunge\system\searchers\elasticSearch
     */
    protected $es;

    public function __construct()
    {
        \grunge\system\debug\debug::put(
            "Constructing...", __METHOD__, 10);
        $this->toolkit = \grunge\system\systemToolkit::getInstance();
        $this->response = $this->output = $this->toolkit->getResponse();
        $this->request = $this->toolkit->getRequest();
        $this->getCache();
    }

    /**
     * @return \grunge\system\renders\view
     */
    public function getView()
    {
        if($this->view == null) {
            $this->view = \grunge\system\renders\view::factory();
        }
        return $this->view;
    }

    /**
     * @return \grunge\system\cache\cache
     */
    public function getCache()
    {
        if($this->cache == null) {
            $this->cache = \grunge\system\cache\cache::factory();
        }
        return $this->cache;
    }

    /**
     * @return \grunge\system\orm\mongoDb
     */
    protected function getMongo($configName = 'mongo')
    {
        static $mongo = [];
        if(!isset($mongo[$configName])) {
            $mongo[$configName] = \grunge\system\orm\database::factory($configName)
                ->getBackend();
        }
        return $mongo[$configName];
    }

    /**
     * @param string $configName
     * @return \grunge\system\searchers\elasticSearch
     */
    protected function getElastic($configName = 'default')
    {
        if($this->es == null) {
            $this->es = \grunge\system\searchers\searcher::factory($configName);
        }
        return $this->es;
    }

}