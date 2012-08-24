<?php
namespace grunge\system\renders;

class view
extends \grunge\system\patterns\singletone_array_factory
implements \grunge\system\interfaces\view {

    protected static $singletone_class = "\\grunge\\system\\renders\\view";
    protected $configVarname = 'view';
    protected $baseNamespace = '\\grunge\\system\\renders\\';
    protected $backendPrefix = "";
    protected $backendPostfix = "View";

    public function __construct($configName)
    {
        parent::$singletone_class = (string)__CLASS__;
        parent::__construct($configName);
    }

    public function render($templateName = '', $asString = true, $cache_id = '')
    {
        return $this->backend->render($templateName, $asString, $cache_id);
    }

    public function assign($param1 = null, $param2 = null)
    {
        if ($param2 != null) {
            return $this->backend->assign($param1, $param2);
        }
        return $this->backend->assign($param1);
    }

    /**
    * put your comment there...
    *
    * @param mixed $configName
    * @return self
    */
    public static function factory($configName = 'default')
    {
        if(!isset(self::$instance[$configName])) {
            self::$instance[$configName] = new self($configName);
        }
        return self::$instance[$configName];
    }
}