<?php
namespace grunge\system;

class systemConfig {

    // Internal params
    private static $initialized = false;
    // External params

    /**
     * Applcation name
     *
     * Required
     *
     * @var string
     */
    public static $application_name = "Grunge application";

    /**
     * Application version
     *
     * Optional
     *
     * @var string
     */
    public static $application_version = "0.1";

    /**
     * Application type
     *
     * Required
     *
     * @example console|web|mixed
     * @var srting
     */
    public static $application_type = 'mixed';

    /**
     * Enabled modules
     *
     * Required
     *
     * @var type
     */
    public static $enabledModules = array(
        'simple',
    );

    /**
     *
     *
     * Optional
     *
     * @var string
     */
    public static $pathToSystem;

    /**
     *
     *
     * Required
     *
     * @var string
     */
    public static $pathToApp;

    /**
     *
     *
     * Optional
     *
     * @var string
     */
    public static $pathToRoot;

    /**
     *
     *
     * Optional (authogenerated)
     *
     * @var string
     */
    public static $pathToTemp;

    /**
     * Main output type
     *
     * Required
     *
     * stdout|file
     * @var string
     */
    public static $output_type = 'stdout';

    /**
     *
     *
     * Required if main output type is file
     *
     * @var string
     */
    public static $output_filepath;

    /**
     * Write data to buffer before output
     *
     * Required
     *
     * @var boolean
     */
    public static $output_buffering = false;

    /**
     * Database config
     *
     * Optional
     *
     * @var array
     */
    public static $db = array(
        'default' => array(
            'type' => 'doctrine',
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => '',
            'user' => 'root',
            'password' => '',
            'charset' => 'UTF8'
        ),
    );

    public static $cache = array(
        'default' => array(
            'type' => 'file',
        ),
        'system' => array(
            'type' => 'system',
        ),
        'memcache' => array(
            'type' => 'memcache',
            'servers' => array(
                array(
                    'host' => 'localhost',
                    'port' => 11211
                )
            )
        ),
    );

    public static $systemCacheConfigName = 'system';

    /**
     * View config
     *
     * Optional
     *
     * @var array
     */
    public static $view = array(
        'default' => array(
            'type' => 'quicky',
            'cache_path' => "/tmp/view_cache"
        )
    );

    public static $searcher = array(
        'default' => array(
            'type' => 'elasticSearch',
            'host' => 'cloud.ilook.ru',
            'port' => 9200,
            'transport' => 'HTTP',
            'index_name' => 'Messages',
            'type_name' => 'Message'
        )
    );

    public static $custom = array();

    public static function alive()
    {
        return self::$initialized;
    }

    /**
     * Init systemConfig
     * Check paths etc.
     */
    public static function init()
    {
        if (self::$pathToRoot == null) {
            self::$pathToRoot = realpath(__DIR__ . "/..");
        }

        if (self::$pathToSystem == null) {
            self::$pathToSystem = __DIR__;
        }

        if (self::$pathToApp == null) {
            self::$pathToApp = realpath(__DIR__ . "/../app");
        }

        if (self::$pathToTemp == null) {
            self::$pathToTemp = self::$pathToApp . '/tmp';
        }

        if (self::$output_filepath == null) {
            self::$output_filepath = self::$pathToApp . '/output';
        }

        if (!is_dir(self::$pathToTemp)) {
            mkdir(self::$pathToTemp, 0777, true);
        }

        self::$initialized = true;
    }

}
