<?php
namespace grunge\system\cache;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gFileCache
 *
 * @author GreeveX <greevex@gmail.com>
 */
class mongoCache implements \grunge\system\interfaces\cache {

    private $autoCommit = true;

    /**
    * put your comment there...
    *
    * @var \grunge\system\orm\mongoDb
    */
    private $mongo;
    private $collection_name = "cache";

    public function commit() {

    }

    public function __construct($configName) {
        $config = \grunge\system\systemConfig::$cache[$configName];
        $this->collection_name = $config['cache_collection'];
        $this->mongo = \grunge\system\orm\database::factory($config['dbconfig_name'])
                ->getBackend();
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
        $data = array(
            'key' => $key,
            'value' => $value,
            'expire' => $expire
        );
        $this->mongo->remove($this->collection_name, array('key' => $key));
        return !!$this->mongo->insert($this->collection_name, $data, true);
        /**
        * @todo strange error on update-create [don't creating row]
        */
        //return $this->mongo->update($this->collection_name, array('key' => $key), array('$set' => $data), true);
    }

    public function get($key)
    {
        $value = $this->mongo
                ->selectOne($this->collection_name, array('key' => $key));
        return ($value !== null) ? $value['value'] : null;
    }

    public function exists($key)
    {
        return ($this->mongo->selectOne($this->collection_name, array(
                    'key' => $key
                )) !== null);
    }

    public function delete($key)
    {
        if($key == '*') {
            return $this->mongo
                ->remove($this->collection_name, array(
                '_id' => array(
                    '$gt' => 0
                )
            ));
        } else {
            return $this->mongo
                    ->remove($this->collection_name, array(
                        'key' => $key
                    ));
        }
    }

    public function clear()
    {
        return false;
    }
}