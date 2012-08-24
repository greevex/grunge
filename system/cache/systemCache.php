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
class systemCache implements \grunge\system\interfaces\cache {

    private $data = array();

    private $autoCommit = false;

    private $cache_filename = null;

    private function getCacheFilename()
    {
        if($this->cache_filename == null) {
            $this->cache_filename = systemConfig::$pathToTemp .
                "/grunge_" .
                str_replace(" ", "_", (systemConfig::$application_name . systemConfig::$application_version)) .
                ".syscache";
        }
        return $this->cache_filename;
    }

    public function commit() {
        $cachefile = $this->getCacheFilename();
        file_put_contents($cachefile, serialize($this->data));
    }

    public function __construct($configName) {
        $cachefile = $this->getCacheFilename();
        if(file_exists($cachefile)) {
            $this->data = unserialize(file_get_contents($cachefile));
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
        $this->data[$key] = $value;
        if($this->autoCommit) {
            $this->commit();
        }
    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function exists($key)
    {
        return isset($this->data[$key]);
    }

    public function delete($key)
    {
        unset($this->data[$key]);
        if($this->autoCommit) {
            $this->commit();
        }
    }

    public function clear()
    {
        $this->data = array();
        $this->commit();
    }
}