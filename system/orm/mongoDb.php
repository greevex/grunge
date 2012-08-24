<?php
namespace grunge\system\orm;

use \grunge\system\systemConfig;

/**
 * Description of doctrine
 *
 * @author GreeveX <greevex@gmail.com>
 */
class mongoDb {

    private static $instance;

    private $mongo;
    private $db;

    public static function factory($configName = 'mongo')
    {
        \grunge\system\debug\debug::put("Loading mongodb... Status: " .
                ((self::$instance == null) ? "not " : "") . "loaded",
                __METHOD__, 8);
        if(self::$instance == null) {
            self::$instance = new self($configName);
        }
        return self::$instance;
    }

    public function __construct($configName = 'mongo')
    {
        $this->mongo = new \Mongo(systemConfig::$db[$configName]['host']);
        $this->db = $this->mongo
                ->selectDB(systemConfig::$db[$configName]['dbname']);
    }

    public function reconnect()
    {
        $this->mongo->connect();
        $this->db->resetError();
    }

    public function insert($collection, &$data, $options = [])
    {
        if(!isset($data['_id'])) {
            $data['_id'] = new \MongoId();
        } elseif(!is_object($data['_id'])) {
            $data['_id'] = new \MongoId($data['_id']);
        }
        return $this->db
                    ->selectCollection($collection)
                    ->insert($data, $options);
    }

    /**
    * select data from Mongo
    *
    * @param string $collection
    * @param array $criteria
    * @param array $fields
    * @return \MongoCursor
    */
    public function select($collection, $criteria = [], $fields = [])
    {
        $data = $this->db
                    ->selectCollection($collection)
                    ->find($criteria, $fields);
        return $data;
    }

    public function selectOne($collection, $criteria = [], $fields = [])
    {

        $data = $this->db
                    ->selectCollection($collection)
                    ->findOne($criteria, $fields);
        return $data;
    }

    public function update($collection, $criteria = [], $update_data = [], $createIfNonExists = false)
    {
        $data = $this->db
                    ->selectCollection($collection)
                    ->update($criteria, ['$set' => $update_data], ($createIfNonExists ? ['upsert' => true] : []));
        return $data;
    }

    public function remove($collection, $criteria, $options = [])
    {
        $data = $this->db
                    ->selectCollection($collection)
                    ->remove($criteria, $options);
        return $data;
    }

    public function getCount($collection)
    {
        try {
            return $this->db
                ->selectCollection($collection)
                ->count();
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getCountBy($collection, $criteria)
    {
        return $this->select($collection, $criteria)->count();
    }

    public function getDatabase()
    {
        return $this->db;
    }
}
