<?php

namespace grunge\system\orm;

use \grunge\system\systemConfig;

class sqliteDb
{

    private static $instance;
    private $db;

    public static function factory($configName = 'sqlite')
    {
        \grunge\system\debug\debug::put("Loading sqliteDb... Status: " .
                ((self::$instance == null) ? "not " : "") . "loaded", __METHOD__, 8);
        if (self::$instance == null) {
            self::$instance = new self($configName);
        }
        return self::$instance;
    }

//    public function __construct($configName = 'sqlite')
//    {
//        $this->db = new \SQLite3("databases/" . systemConfig::$db[$configName]['host']. ".sqlite");
//    }

    public function useDatabase($db_name)
    {
        $path = systemConfig::$pathToApp . "/databases";
        $this->db = new \SQLite3("{$path}/" . $db_name . ".sqlite", SQLITE3_OPEN_READWRITE);
    }

//    public function useDatabase($db_name)
//    {
//        $path = system\systemConfig::$pathToApp . "/databases";
////        SQLITE3_OPEN_CREATE
//
//    }

    public function select($criteria)
    {
        if (!isset($criteria['fields'])) {
            $criteria['fields'] = '*';
        }
        if (is_array($criteria['fields'])) {
            $criteria['fields'] = implode(', ', $criteria['fields']);
        }
        $sql = "SELECT \n\t{$criteria['fields']} \nFROM \n\t'{$criteria['table']}'";
        if (isset($criteria['where'])) {
            $sql .= " \nWHERE ";
            if (is_array($criteria['where'])) {
                $where = array();
                foreach ($criteria['where'] as $row) {
                    $where[] = "{$row['key']} {$row['comparsion']} '{$row['value']}'";
                }
                $sql .= implode(" \n\tAND ", $where);
            } else {
                $sql .= $criteria['where'];
            }
        }
        if (isset($criteria['order_by'])) {
            $sql .= " \nORDER BY {$criteria['order_by']} ASC";
        }
        if (isset($criteria['limit'])) {
            $sql .= " \nLIMIT {$criteria['limit']}";
        }
        if (isset($criteria['offset'])) {
            $sql .= " \nOFFSET {$criteria['offset']}";
        }

        return $this->db->query($sql . ";");
    }

    public function insert($criteria)
    {
        $sql = "INSERT INTO '{$criteria['table']}' VALUES({$criteria['values']})";
        $this->db->query($sql . ";");
    }

    public function getDatabase()
    {
        return $this->db;
    }

    public function delete($criteria)
    {
        $sql = "";
        foreach ($criteria as $table => $data) {
            $sql = "DELETE FROM `{$table}` WHERE ";
            $where = array();
            foreach ($data as $how => $value) {
                $where = "`{$value['key']}` {$value['comparsion']} '{$value['value']}'";
            }
            $sql .= implode(' AND ', $where);
        }
        return $this->db->query($sql . ";");
    }

    public function query($query)
    {
        return $this->db->query($query . ";");
    }

    public function exec($query)
    {
        return $this->db->exec($query . ";");
    }

    protected function beginTransaction()
    {
        return $this->db->query("BEGIN;");
    }

    protected function endTransaction()
    {
        return $this->db->query("COMMIT;");
    }

}
