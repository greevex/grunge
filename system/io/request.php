<?php
namespace grunge\system\io;

use grunge\system\systemConfig;

/**
 * Description of iResponse
 *
 * @author GreeveX <greevex@gmail.com>
 */
class request
extends \grunge\system\patterns\singleton
{

    private static $request = null;

    const SC_GET = '_GET';
    const SC_POST = '_POST';
    const SC_REQUEST = '_REQUEST';
    const SC_FILES = '_FILES';
    const SC_ARGV = 'argv';
    const SC_INTERNAL = 'self::$INTERNAL';
    const NOT_EXISTS = 'param_not_exists';

    private static $INTERNAL = array();

    public function __construct()
    {

    }

    public function importArguments($request_type = self::SC_REQUEST, $clean = true)
    {
        switch($request_type)
        {
            case self::SC_FILES:
                $data = $_FILES;
                break;
            case self::SC_GET:
                $data = $_GET;
                break;
            case self::SC_POST:
                $data = $_POST;
                break;
            case self::SC_REQUEST:
                $data = $_REQUEST;
                break;
        }
        if($clean) {
            self::$INTERNAL = $data;
        } else {
            self::$INTERNAL = array_merge_recursive(self::$INTERNAL, $data);
        }
    }

    public function export()
    {
        return self::$INTERNAL;
    }

    public function setArguments($array, $clean = true)
    {
        if($clean) {
            self::$INTERNAL = $array;
        } else {
            self::$INTERNAL = array_merge_recursive(self::$INTERNAL, $array);
        }
    }

    private function getParam($name, $request_type)
    {
        $req = $request_type == self::SC_INTERNAL ? self::$INTERNAL : ${$request_type};
        return isset($req[$name]) ?
                        $req[$name] : self::NOT_EXISTS;
    }

    public function getString($name, $request_type = self::SC_INTERNAL)
    {
        $param = $this->getParam($name, $request_type);
        if($param === self::NOT_EXISTS) {
            return null;
        }
        return strval($param);
    }

    public function getBoolean($name, $request_type = self::SC_INTERNAL)
    {
        $param = $this->getParam($name, $request_type);
        if($param === self::NOT_EXISTS) {
            return null;
        }
        return (bool)($param == true || $param == 'true');
    }

    public function getInteger($name, $request_type = self::SC_INTERNAL)
    {
        $param = $this->getParam($name, $request_type);
        if($param === self::NOT_EXISTS) {
            return null;
        }
        return intval($param);
    }

    public function getFloat($name, $request_type = self::SC_INTERNAL)
    {
        $param = $this->getParam($name, $request_type);
        if($param === self::NOT_EXISTS) {
            return null;
        }
        return floatval($param);
    }

    public function getArray($name, $request_type = self::SC_INTERNAL)
    {
        $param = $this->getParam($name, $request_type);
        if($param === self::NOT_EXISTS) {
            return null;
        }
        return (array)$param;
    }
}