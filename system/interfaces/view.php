<?php
namespace grunge\system\interfaces;

/**
 * Interface view 
 */
interface view {
    
    public function __construct($configName);
    
    public function render($templateName = '', $asString = true, $cache_id = '');
    
    public function assign($var, $value = null);
}