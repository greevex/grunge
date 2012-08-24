<?php
namespace grunge\system\orm;

use \grunge\system\debug\debug;

/**
 * Description of mysqlDb
 *
 * @author GreeveX <greevex@gmail.com>
 */
class mysqlDb
implements \grunge\system\interfaces\ormdb {

    /**
     *
     * @var \grunge\libs\PdoWrapper\PdoWrapper
     */
    private $db;

    public function getBackend()
    {
        return $this->db;
    }

    public function __construct($configName)
    {
        $this->db = new \grunge\libs\PdoWrapper\PdoWrapper();
        $this->db->pdoConnect(  \grunge\system\systemConfig::$db[$configName]['host'],
                                \grunge\system\systemConfig::$db[$configName]['user'],
                                \grunge\system\systemConfig::$db[$configName]['password'],
                                \grunge\system\systemConfig::$db[$configName]['dbname'] );
        $this->db->pdoExecute("SET NAMES `UTF8`");
    }

    public function selectDb($dbname)
    {
        $this->db->run("USE `{$dbname}`;");
    }

    public function save($object)
    {
        return $result;
    }

    public function select($criteria, $many = true)
    {
        if(!isset($criteria['fields'])) {
            $criteria['fields'] = '*';
        }
        if(is_array($criteria['fields'])) {
            $criteria['fields'] = implode(', ', $criteria['fields']);
        }
        $sql = "SELECT \n\t{$criteria['fields']} \nFROM \n\t{$criteria['table']}";
        if(isset($criteria['where'])) {
            $sql .= " \nWHERE ";
            if(is_array($criteria['where'])) {
                $where = array();
                foreach($criteria['where'] as $row) {
                    $where[] = "{$row['key']} {$row['comparsion']} '{$row['value']}'";
                }
                $sql .= implode(" \n\tAND ", $where);
            } else {
                $sql .= $criteria['where'];
            }
        }
        if(isset($criteria['order_by'])) {
            $sql .= " \nORDER BY {$criteria['order_by']} ASC";
        }
        if(isset($criteria['limit'])) {
            $sql .= " \nLIMIT {$criteria['limit']}";
        }
        if(isset($criteria['offset'])) {
            $sql .= " \nOFFSET {$criteria['offset']}";
        }
        /*
        \grunge\system\io\response::getInstance()
                ->writeLn("===")
                ->writeLn($sql)
                ->writeLn("=/=");
        /**/
        if($many) {
            debug::put("pdoGetAll:{$sql}", __METHOD__, 10);
            return $this->db->pdoGetAll($sql);
        } else {
            debug::put("pdoGetRow:{$sql}", __METHOD__, 10);
            return $this->db->pdoGetRow($sql);
        }
    }

    public function update($array)
    {
        foreach($array['data'] as $row) {
            $this->db->pdoInsUpd(
                        $array['table'], $row, 'update', $array['where']
                        );
        }
    }

    /**
     * @example
     * array(
     *  'table` =>  array(
     *          array(
     *              'key' => '',
     *              'comparsion' => '',
     *              'value' => ''
     *          ),
     *          ...
     *      )
     * )
     *
     * @param type $object
     */
    public function delete($object)
    {
        $sql = "";
        foreach($array as $table => $data) {
            $sql = "DELETE FROM `{$table}` WHERE ";
            $where = array();
            foreach($data as $how => $value) {
                $where = "`{$value['key']}` {$value['comparsion']} '{$value['value']}'";
            }
            $sql .= implode(' AND ', $where);
        }
        return $this->db->pdoExecute($sql);
    }

}