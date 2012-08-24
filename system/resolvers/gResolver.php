<?php
namespace grunge\system\resolvers;

/**
 * Description of gResolver
 *
 * @author GreeveX <greevex@gmail.com>
 */
abstract class gResolver {
    
    public function __construct()
    {
        \grunge\system\systemConfig::alive();
    }
    
    abstract public function resolve($file);
}