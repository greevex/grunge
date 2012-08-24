<?php
namespace grunge\system\orm;

/**
 * Description of database
 *
 * @author GreeveX <greevex@gmail.com>
 */
class database
implements \grunge\system\interfaces\ormdb {

    /**
     * @var \grunge\system\interfaces\ormdb
     */
    private $backend;

    private static $instance = array();

    private $config = array();

    /**
     * 
     * @return \grunge\system\interfaces\ormdb
     */
    public function getBackend()
    {
        \grunge\system\debug\debug::put(
            "backend " . get_class($this->backend) . " requested", __METHOD__, 8);
        return $this->backend;
    }

    public function __construct($configName)
    {
        \grunge\system\debug\debug::put(
            "configName:{$configName}", __METHOD__, 8);
        $this->config = \grunge\system\systemConfig::$db[$configName];
        $backendClass = $this->typeClass($this->config['type']);
        $this->backend = new $backendClass($configName);
    }

    public function selectDb($dbname)
    {
        \grunge\system\debug\debug::put(
            "dbname:{$dbname}", __METHOD__, 8);
        return $this->backend->selectDb($dbname);
    }

    public function insert($object)
    {
        \grunge\system\debug\debug::put(
            json_encode($object), __METHOD__, 8);
        return $this->backend->insert($object);
    }

    public function update($object)
    {
        \grunge\system\debug\debug::put(
            json_encode($object), __METHOD__, 8);
        return $this->backend->update($object);
    }

    public function select($criteria, $many = true)
    {
        \grunge\system\debug\debug::put(
            json_encode(array($criteria, $many)), __METHOD__, 8);
        return $this->backend->select($criteria, $many);
    }

    public function delete($object)
    {
        \grunge\system\debug\debug::put(
            json_encode($object), __METHOD__, 8);
        return $this->backend->delete($object);
    }

    /**
     * @static
     * @param string $configName
     * @return database
     */
    public static function factory($configName = 'default')
    {
        \grunge\system\debug\debug::put("configName:{$configName}", __METHOD__, 8);
        if(!isset(self::$instance[$configName])) {
            self::$instance[$configName] = new self($configName);
        }
        return self::$instance[$configName];
    }

    private function typeClass($name)
    {
        $converted = "\\grunge\\system\\orm\\{$name}Db";
        \grunge\system\debug\debug::put(
                "Convert type from {$name} to {$converted}", __METHOD__, 8);
        return $converted;
    }
}
