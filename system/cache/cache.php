<?php
namespace grunge\system\cache;

class cache
implements \grunge\system\interfaces\cache
{

    static $instance = array();

    private $backend;

    /**
     *
     * @param type $type
     * @return \grunge\system\cache\cache
     */
    public static function factory($configName = 'default')
    {
        $type = 'file';
        \grunge\system\debug\debug::put(
                "Factoring cache {$configName} cache...", __METHOD__, 10);
        if(!isset(self::$instance[$configName])) {
            self::$instance[$configName] = new self($configName);
        }
        return self::$instance[$configName];
    }

    public function __construct($configName = 'default')
    {
        $config = \grunge\system\systemConfig::$cache[$configName];
        require_once GRUNGE_PATH . "/system/cache/{$config['type']}Cache.php";
        $type = "\\grunge\\system\\cache\\{$config['type']}Cache";
        $this->backend = new $type($configName);
    }

    public function set($key, $value, $expire = 3600)
    {
        \grunge\system\debug\debug::put(
                "Setting data by key {$key}", __METHOD__, 10);
        return $this->backend->set($key, $value, $expire);
    }

    public function get($key)
    {
        \grunge\system\debug\debug::put(
                "Getting data by key {$key}", __METHOD__, 10);
        return $this->backend->get($key);
    }

    public function exists($key)
    {
        $exists = $this->backend->exists($key);
        \grunge\system\debug\debug::put(
                "Check data by key {$key} > " . ($exists ? 'true' : 'false'), __METHOD__, 10);
        return $exists;
    }

    public function delete($key)
    {
        \grunge\system\debug\debug::put(
                "Deleting data by key {$key}", __METHOD__, 10);
        return $this->backend->delete($key);
    }

    public function clear()
    {
        \grunge\system\debug\debug::put(
                "Clearing cache...", __METHOD__, 10);
        return $this->backend->clear();
    }

    public function enableAutoCommit()
    {
        \grunge\system\debug\debug::put(
                "AutoCommit = enabled", __METHOD__, 10);
        return $this->backend->enableAutoCommit();
    }

    public function disableAutoCommit()
    {
        \grunge\system\debug\debug::put(
                "AutoCommit = disabled", __METHOD__, 10);
        return $this->backend->disableAutoCommit();
    }

    public function commit()
    {
        \grunge\system\debug\debug::put(
                "Commiting...", __METHOD__, 10);
        return $this->backend->commit();
    }
}