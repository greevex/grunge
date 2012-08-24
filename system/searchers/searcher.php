<?php
namespace grunge\system\searchers;

/**
 * Description of searcher
 *
 * @author GreeveX <greevex@gmail.com>
 */
class searcher {
    
    private static $backend;
    
    public static function factory($configName = 'default')
    {
        if(!isset(self::$backend[$configName])) {
            $config = \grunge\system\systemConfig::$searcher[$configName];
            $searcher = "\\grunge\\system\\searchers\\{$config['type']}";
            self::$backend[$configName] = new $searcher($configName);
        }
        return self::$backend[$configName];
    }
    
}