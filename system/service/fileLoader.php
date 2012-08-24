<?php
namespace grunge\system\service;

class fileLoader
{
    /**
    * Список всех загруженных файлов
    *
    * @var array
    */
    private static $files = array();

    /**
     * Cache
     *
     * @var \grunge\system\cache\cache
     */
    private static $cache = null;

    protected static $registered = array(
        GRUNGE => GRUNGE_PATH
    );

    /**
     * @var array|\grunge\system\resolvers\gResolver
     */
    private static $resolvers;

    /**
     * @static
     * @return \grunge\system\cache\cache
     */
    private static function getCache()
    {
        if(isset(self::$files['grunge\system\cache\cache']) && self::$cache == null) {
            self::$cache = \grunge\system\cache\cache::factory(\grunge\system\systemConfig::$systemCacheConfigName);
        }
        return self::$cache;
    }

    public static function getLoadStack()
    {
        \grunge\system\debug\debug::put(
            "Load stack requested", __METHOD__, 4);
        return self::$files;
    }

    public static function get($file)
    {
        \grunge\system\debug\debug::put("file:{$file}", __METHOD__, 4);
        $key = md5("FG{$file}");
        if(self::getCache()->exists($key)) {
            $filepath = self::getCache()->get($key);
            \grunge\system\debug\debug::put(
                        "Loaded from cache! > {$filepath}", __METHOD__, 4);
        } else {
            $file = str_replace("\\", '/', $file);
            $filepath = self::resolve($file);
            if ($filepath === false) {
                return false;
            }
            \grunge\system\debug\debug::put(
                "Searching OK!", __METHOD__, 4);
            self::getCache()->set($key, $filepath);
        }
        self::$files[$file] = 1;
        try {
            return file_get_contents($filepath);
        } catch(\Exception $e) {
            \grunge\system\debug\debug::put(
                "{$e->getMessage()} > {$filepath}", __METHOD__, 4);
            print $e->getMessage();
        }
    }

    public static function loadJson($file, $as_array = false)
    {
        return json_decode(self::get($file), $as_array);
    }

    public static function load($file, $force = 0)
    {
        $key = md5("FL{$file}");
        \grunge\system\debug\debug::put("File:{$file} [force:" . ($force ? 'Yes' : 'No') . "]", __METHOD__, 4);
        if (!isset(self::$files[$key]) || $force) {
            if(self::$cache->exists($key)) {
                $filepath = self::$cache->get($key);
                \grunge\system\debug\debug::put(
                            "Loaded from cache! > {$filepath}", __METHOD__, 4);
            } else {
                $file = str_replace("\\", '/', $file);
                $filepath = self::resolve($file);
                if ($filepath === false) {
                    \grunge\system\debug\debug::put(
                        "Searching FAIL! > {$file}", __METHOD__, 4);
                    return false;
                }
                \grunge\system\debug\debug::put(
                    "Searching OK!", __METHOD__, 4);
                self::$cache->set($key, $filepath);
            }
            self::$files[$key] = 1;
            try {
                return require $filepath;
            } catch(\Exception $e) {
                \grunge\system\debug\debug::put(
                    "{$e->getMessage()} > {$filepath}", __METHOD__, 4);
                print $e->getMessage();
            }
        }
        \grunge\system\debug\debug::put(
            "Already loaded! > {$file}", __METHOD__, 8);
        return true;
    }

    public static function addResolver($resolver, $name = null)
    {
        if($name == null) {
            $name = count(self::$resolvers);
        }
        \grunge\system\debug\debug::put(
            "Added resolver {{$name}} > " . get_class($resolver), __METHOD__, 8);
        if(!in_array('grunge\system\resolvers\gResolver', class_parents($resolver))) {
            throw new \grunge\system\exceptions\gException(
                    "Argument for " . __METHOD__ . " must be an instance of grunge\\system\\service\\gResolver"
                    );
        }
        self::$resolvers[$name] = $resolver;
        \grunge\system\debug\debug::put(
            "Resolvers count: " . count(self::$resolvers), __METHOD__, 2);
    }

    public static function addResolvers(array $resolvers)
    {
        foreach($resolvers as $name => $resolver) {
            self::addResolver($resolver, $name);
        }
    }

    public static function resolve($file)
    {
        \grunge\system\debug\debug::put(
                "Resolving file {$file}", __METHOD__, 2);
        $path = self::find($file);
        if($path === false && self::$resolvers != null) {
            foreach(self::$resolvers as $resolver) {
                $path = $resolver->resolve($file);
                if($path !== false) {
                    break;
                }
            }
        }
        return $path;
    }

    public static function getResolverByName($name)
    {
        if(!isset(self::$resolvers[$name])) {
            return false;
        }
        return self::$resolvers[$name];
    }

    protected static function registerNamespace($namespace_base, $path)
    {
        self::$registered[$namespace_base] = $path;
    }

    protected static function unregisterNamespace($namespace_base)
    {
        if(isset(self::$registered[$namespace_base])) {
            unset(self::$registered[$namespace_base]);
        }
    }

    public static function find($file)
    {
        $namespace_base = mb_substr($file, 0, strpos($file, '/'));
        if(!isset(self::$registered[$namespace_base])) {
            $namespace_base = GRUNGE;
        }
        \grunge\system\debug\debug::put(
            "Searching: {$file}", __METHOD__, 2);
        if(file_exists($file)) {
            return $file;
        }
        $file = str_replace("{$namespace_base}/", '', $file);
        $filepath = self::$registered[$namespace_base] . "/{$file}.php";
        \grunge\system\debug\debug::put(
            "Searching: {$filepath}", __METHOD__, 2);
        if(file_exists($filepath)) {
            return $filepath;
        }
        $filepath = self::$registered[$namespace_base] . "/{$file}";
        \grunge\system\debug\debug::put(
            "Searching: {$filepath}", __METHOD__, 2);
        if(file_exists($filepath)) {
            return $filepath;
        }
        return false;
    }

    public static function registerSpl()
    {
        require GRUNGE_PATH . '/system/systemConfig.php';
        require GRUNGE_PATH . '/system/interfaces/cache.php';
        require GRUNGE_PATH . '/system/cache/cache.php';
        self::$files['grunge\system\cache\cache'] = 1;
        self::$files['grunge\system\interfaces\cache'] = 1;
        self::$cache = \grunge\system\cache\cache::factory(\grunge\system\systemConfig::$systemCacheConfigName);

        \grunge\system\debug\debug::put("Registering spl...", __METHOD__, 2);
        spl_autoload_extensions('.php');
        spl_autoload_register(array(__CLASS__, 'load'), 1);
    }

    public static function unregisterSpl()
    {
        \grunge\system\debug\debug::put("Unregistering spl...", __METHOD__, 2);
        spl_autoload_unregister(array(__CLASS__, 'load'));
    }
}