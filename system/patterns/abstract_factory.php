<?php
namespace grunge\system\patterns;

use \grunge\system\systemConfig;

/**
 * Class {$classname}
 *
 * @version 1.0
 * @author greevex <greevex@gmail.com>
 */
abstract class abstract_factory
{
    private static $facto = array();

    protected $base_namespace = '';

    public function __construct($name)
    {
        $classname = self::$base_namespace .
                     $this->backendPrefix .
                     systemConfig::${$this->configVarname}[$configName]['type'].
                     $this->backendPostfix;
        $this->backend = new $classname($configName);
    }

    public static function factory($name = null)
    {
        if(!isset($this->instance[$name])) {
            $class = get_called_class();
            $this->instance = new $class;
        }
        return $this->instance;
    }
}