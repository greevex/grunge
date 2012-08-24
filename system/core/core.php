<?php
namespace grunge\system\core;

/**
 * Description of core
 *
 * @author GreeveX <greevex@gmail.com>
 */
class core {
    private static $loaded = false;

    public static function isLoaded()
    {
        return self::$loaded;
    }

    public static function loadSystem()
    {
        \grunge\system\debug\debug::put(
            "System load requested. Status:" . (self::$loaded ? '' : ' not ') . " loaded", __METHOD__, 5);
        if(self::$loaded) {
            throw new \gException("Attempt to load core for two times!");
        }
        \grunge\system\service\fileLoader::addResolvers(
                array(
                    'module' => new \grunge\system\resolvers\moduleResolver(),
                    'view' => new \grunge\system\resolvers\viewResolver()
                ));
        self::$loaded = true;
    }
}