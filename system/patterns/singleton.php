<?php
namespace grunge\system\patterns;

/**
 * Class singleton
 *
 * @version 1.0
 * @author greevex <greevex@gmail.com>
 */
abstract class singleton
{
    private static $instance;

    public static function getInstance()
    {
        if(self::$instance == null) {
            $class = get_called_class();
            self::$instance = new $class();
        }
        return self::$instance;
    }
}