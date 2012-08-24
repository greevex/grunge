<?php
namespace grunge\libs\PdoWrapper;
#################################################
/*
 * class PdoWrapper - wrapper for the PHP PDO functions
 *
 * methods:
 * pdoConnect      - connects to database
 * pdoExecute      - executes an SQL query, can have SQL parameters
 * pdoRowCount     - returns number of rows affected
 * pdoGetAll       - returns all rows in a 2-dimensional hash array
 * pdoGetAssoc     - returns all rows in a 2-dimensional hash array, 1st retrieved column is index of array
 * pdoGetRow       - returns the first row of results in a 1-dimensional array
 * pdoInsUpd       - creates an insert or update statement from tablename and array with data
 * pdoLastInsertId - returns last created ID from an autoincrement column
 * pdoException    - displays error message after exception
 * 
 * Licensed under the terms of the BSD License:
 *      http://www.opensource.org/licenses/bsd-license.php
 *
 * ****************************************************
 * You must copy this copyright notice into any modification of this class
 * ****************************************************
 * Copyright by:
 * Author    Emile Schenk
 *           http://www.donauweb.at
 *           https://www.xing.com/profile/Emile_Schenk
 * License   BSD
 * ****************************************************
 * End copyright notice 
 * ****************************************************
 * Created   February 2010
 * Revision  1.1
 * 
 * Changes 
 *     Revision 1.0
 *         - Initial release
 *         - well tested, is in use in commercial projects
 *     Revision 1.1
 *         - More comments added
 *     Revision 1.2
 *         - pdoInsUpd: Older PHP version need separate variables when the same value is used several times in one SQL statement
 */ 

##################################################################################################
##################################################################################################
class PdoWrapper {
    private $dbh;
#################################################
# When useTablecols==1 a file must be included which defines all fields of tables. 
# Only fields that are defined in this file will be INSERTED/UPDATED in the table.
# Example:
# global $tablecols;
# $tablecols['customers'] = array('id','firstname','lastname','address','postcode','city'); 
#################################################
    public $useTablecols = 0;
#################################################
# use nicely formatted output of datadumper: https://sourceforge.net/projects/datadumper
# for output of Exceptions
#################################################
    public $useDatadumper = 0;

#################################################
function pdoConnect($server, $username, $password, $database) {
    try {
        $this->dbh = new \PDO("mysql:dbname=$database;host=$server", $username, $password);
    } 
    catch(Exception $e) {
        $this->pdoException( $e->getMessage() );
      exit;
    }
    $this->dbh->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
  return true;
}


#################################################
function pdoExecute($query, $array=array()) {
    $this->sql = array('query'=>$query, 'input'=>$array);
    try{
        $stmt = $this->dbh->prepare($query);
        $ret = $stmt->execute($array);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $stmt;
    }
    catch (Exception $e) {
        $this->pdoException( $e->getMessage() );
      exit;
    }
}

#################################################
function pdoRowCount($stmt) {
    try{
        $count = $stmt->rowCount();
        return $count;
    }
    catch (Exception $e) {
        $this->pdoException( $e->getMessage() );
      exit;
    }
}

#################################################
function pdoGetAll($query, $array=array()) {
    $stmt = $this->pdoExecute($query, $array);
    $rows = $stmt->fetchAll();
    return $rows;
}

#################################################
function pdoGetAssoc($query, $array=array()) {
    $rows = $this->pdoGetAll($query, $array);
    foreach($rows as $row) {
        $idx = array_shift($row);
        $rows_assoc[$idx] = $row;
    }
    return $rows_assoc;
}

#################################################
function pdoGetRow($query, $array=array()) {
    $stmt = $this->pdoExecute($query, $array);
    $row = $stmt->fetch();
    return $row;
}

#################################################
/* function pdoInsUpd
 * Parameters:
 * $table: name of table 
 * $data: hash-array with values of data for INSERT/UPDATE
 * $action: 'insert' | 'insert duplicate' | 'insert delayed' | 'update'
 *          duplicate adds to SQL: ON DUPLICATE KEY UPDATE
 *          delayed adds to SQL: DELAYED
 * $parameters: WHERE parameters for UPDATE
 *              hash-array
 *              index can be: fieldname | 'fieldname <operator>' where operator can be '=', '<', '>', '<>', '>=', '<=', 'like'
 *              when no operator is mentioned '=' is taken as operator
 *              e.g. array('myfield', 'myvalue') results in "WHERE myfield='myvalue'"
 *              e.g. array('myfield >', 'myvalue') results in "WHERE myfield>'myvalue'"
 *              e.g. array('myfield like', 'myvalue') results in "WHERE myfield like '%myvalue%'"
 *              
 * When $this->useTablecols==1 a file must be included which defines all fields of the used table. 
 * Only fields that are defined in this file will be INSERTED/UPDATED in the table.
 * Example of file:
 * global $tablecols;
 * $tablecols['customers'] = array('id','firstname','lastname','address','postcode','city'); 
*/
#################################################
function pdoInsUpd($table, $data, $action = 'insert', $parameters = '') {
  global $tablecols;
  
    $insertcolumns = "";
    $insertvalues = "";
    $duplicatevalues = "";
    $updatevalues = "";
  
  reset($data);
    $this->sql = '';

  foreach($data as $idx=>$d) {
    if($this->useTablecols==1) {
        if(!is_array($tablecols[$table])) break;
        if(!in_array($idx, $tablecols[$table])) continue;
      }

    switch (strtolower((string)$d)) {
      case 'now()':
      case 'null':
            $insertcolumns .= "$idx, ";
            $insertvalues .= "$d, ";
            $updatevalues .= "$idx=$d, ";
      break;
      default:
            $insertcolumns .= "$idx, ";
            $insertvalues .= ":$idx, ";
            $duplicatevalues .= "$idx=:dup_$idx, ";
            $updatevalues .= "$idx=:$idx, ";
            if(is_null($d)) $d = '';
            $insertarray[$idx] = $d;
            $duplicatearray['dup_'.$idx] = $d;
      break;
    }
  }
    if(strlen($insertcolumns) <3) {
    $txt = '';
    if($this->useTablecols==1) {
        $txt = "table $table not found in tablecols.php or ";
    }
        $this->pdoException($txt . "no columns to insert");
      exit;
    }
    $insertcolumns    = rtrim($insertcolumns, ', ');
    $insertvalues     = rtrim($insertvalues, ', ');
    $duplicatevalues  = rtrim($duplicatevalues, ', ');
    $updatevalues     = rtrim($updatevalues, ', ');

  $action = strtolower($action);
     $delayed = '';
  if(substr_count($action, 'delayed') >0) {
      $delayed = 'DELAYED';
  }
  if(strpos($action, 'ignore') !== false) {
      $delayed = 'IGNORE';
  }
  if(substr_count($action, 'insert') >0) {
    $query = "INSERT IGNORE INTO $table ($insertcolumns) VALUES ($insertvalues)";
    if(substr_count($action, 'duplicate') >0) {
        $query .= " ON DUPLICATE KEY UPDATE $duplicatevalues";
        $insertarray = array_merge($insertarray, $duplicatearray);
    }
  } 
  elseif(substr_count($action, 'update') >0) {
    $operators = array('=', '<', '>', '<>', '>=', '<=', 'like');
    foreach($parameters as $idx=>$parameter) {
        $idx_arr = explode(' ', $idx);
        $where_var = '';
        $where_operator = '=';
        if(isset($idx_arr[0])) {
            $where_var = $idx_arr[0];
        }
        if(isset($idx_arr[1])) {
            $where_operator = $idx_arr[1];
        }
        if(!in_array($where_operator, $operators)) {
            continue;
        }
        if($where_operator == 'like') {
            $parameter = "%$parameter%";
        }
        $where_array[] = "$where_var $where_operator :where_$idx";
        $insertarray['where_'.$idx] = $parameter;
    }
    $where = implode(' AND ', $where_array);
    $query = "UPDATE $table SET $updatevalues WHERE $where";
  }
  return $this->pdoExecute($query, $insertarray);
}

#################################################
function pdoLastInsertId() {
    return $this->dbh->lastInsertId();
}

#################################################
function pdoException($message) {
    $pdo_error[error] = 'PDO-SQL-ERROR';
    $pdo_error[PDO_error] = $message;
    $pdo_error[SQL] = $this->sql;
    $pdo_error[debug_backtrace]= debug_backtrace();
    if($this->useDatadumper) {
        de($pdo_error);
    }
    else {
        var_dump($pdo_error);
        exit;
    }
}

#################################################
} # end class






