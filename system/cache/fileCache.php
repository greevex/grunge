<?php
namespace grunge\system\cache;

use \grunge\system\systemConfig;

/**
 * Description of gFileCache
 *
 * @author GreeveX <greevex@gmail.com>
 */
class fileCache
implements \grunge\system\interfaces\cache
{

    private $data = array();

    private $autoCommit = true;

    private $cache_filename = null;

    private function getCacheFilename()
    {
        if($this->cache_filename == null) {
            $this->cache_filename = systemConfig::$pathToTemp .
                "/grunge_" .
                str_replace(" ", "_", (systemConfig::$application_name . systemConfig::$application_version)) .
                ".filecache";
        }
        return $this->cache_filename;
    }

    public function commit()
    {
        $cachefile = $this->getCacheFilename();
        file_put_contents($cachefile, serialize($this->data));
    }

    public function __construct($configName)
    {
        $this->loadData();
    }

    protected function loadData()
    {
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
        if($key == '*') {
            $total = count($this->data);
            $c = 0;
            foreach($this->data as $key => $value) {
                if($c < ceil($total/2)) {
                    unset($this->data[$key]);
                }
                $c++;
            }
            if($this->autoCommit) {
                $this->commit();
            }
        } else {
            unset($this->data[$key]);
            if($this->autoCommit) {
                $this->commit();
            }
        }
    }

    public function clear()
    {
        $this->data = array();
        $this->commit();
    }
}