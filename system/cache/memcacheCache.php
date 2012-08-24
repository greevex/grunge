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
class memcacheCache implements \grunge\system\interfaces\cache {
    
    private $data = array();
    
    private $autoCommit = true;

    private $cache_filename = null;

    private $memcache = null;

    public function commit()
    {

    }
    
    public function __construct($configName)
    {
        $config = systemConfig::$cache[$configName];
        $this->memcache = new \Memcached();
        foreach($config['servers'] as $server) {
            $this->memcache->addServer($server['host'], $server['port']);
        }
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
        $this->memcache->set($key, $value, $expire);
    }
    
    public function get($key)
    {
        return $this->memcache->get($key);
    }
    
    public function exists($key)
    {
        return !($this->memcache->get($key) === false || $this->memcache->get($key) === null);
    }
    
    public function delete($key)
    {
        return $this->memcache->delete($key);
    }
    
    public function clear()
    {
        return $this->memcache->flush();
    }
}