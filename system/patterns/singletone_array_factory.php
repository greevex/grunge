<?php
namespace grunge\system\patterns;

use \grunge\system\systemConfig;

/**
 * Class {$classname}
 * 
 * @version 1.0
 * {if $author}@author {$author}{/if}
 */
abstract class singletone_array_factory
{
    /**
     * Name of the variable in the systemConfig class that contains
     * setups for this class
     * reserved vars: type
     * 
     * @var string
     */
    protected $configVarname;
    
    protected static $singletone_class;
    
    protected $baseNamespace;
    
    protected $backendPrefix = "";
    
    protected $backendPostfix = "";
    
    protected $backend;
    
    protected static $instance = array();
    
    public function __construct($configName)
    {
        $classname = $this->baseNamespace .
                     $this->backendPrefix .
                     systemConfig::${$this->configVarname}[$configName]['type'].
                     $this->backendPostfix;
        $this->backend = new $classname($configName);
    }
    
    public function getBackend()
    {
        return $this->backend;
    }
}