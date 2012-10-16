<?php
namespace grunge\system\cache;

use \grunge\system\systemConfig;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gFileCache
 *
 * @author GreeveX <greevex@gmail.com>
 */
class redisCache
implements \grunge\system\interfaces\cache {
    
    private $autoCommit = true;

    private $configName = "default";

    /**
     * @return \Redis
     */
    private function _factory()
    {
        static $instance;
        if($instance == null) {
            $instance = new \Redis();
            $config = systemConfig::$cache[$this->configName];
            $instance->pconnect($config['server']['host'], $config['server']['port'], $config['server']['timeout']);
            $instance->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            $instance->setOption(\Redis::OPT_PREFIX, $config['server']['prefix']);
        }
        return $instance;
    }

    public function commit()
    {

    }
    
    public function __construct($configName)
    {
        $this->configName = $configName;
    }

    public function enableAutoCommit()
    {
        $this->autoCommit = true;
    }
    
    public function disableAutoCommit()
    {
        $this->autoCommit = false;
    }
    
    public function set($key, $value, $expire = 0)
    {
        return $this->_factory()->setex($key, $expire, $value);
    }
    
    public function get($key)
    {
        return $this->_factory()->get($key);
    }
    
    public function exists($key)
    {
        return $this->_factory()->exists($key);
    }
    
    public function delete($key)
    {
        return $this->_factory()->delete($key);
    }
    
    public function clear()
    {
        return $this->_factory()->flushDB();
    }
}