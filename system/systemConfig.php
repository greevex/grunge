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
    public static $application_name = "Grunge Framework Default Config";

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
    public static $application_type = 'console';

    /**
     * Enabled modules
     *
     * Required
     *
     * @var type
     */
    public static $enabledModules = [];

    /**
     *
     *
     * Optional
     *
     * @var string
     */
    public static $pathToSystem = '';

    /**
     *
     *
     * Required
     *
     * @var string
     */
    public static $pathToApp = '';

    /**
     *
     *
     * Optional
     *
     * @var string
     */
    public static $pathToRoot = '';

    /**
     *
     *
     * Optional (authogenerated)
     *
     * @var string
     */
    public static $pathToTemp = '';

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
    public static $output_filepath = '';

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
    public static $db = [];

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
    public static $view = [];

    public static $custom = [];

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
