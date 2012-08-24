<?php
namespace grunge\system;

/**
* systemToolkit - PHP class, used by project and contained all needed libraries and objects.
*
* PHP Version 5
*
* @author GreeveX <greevex@gmail.com>
*/

class systemToolkit
{
    private static $instance;

    /**
     *
     * @return \grunge\system\systemToolkit
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @return \grunge\system\io\response
     */
    public function getResponse()
    {
        return io\response::getInstance();
    }

    /**
     *
     * @return \grunge\system\orm\database
     */
    public function getDatabase()
    {
        return orm\database::factory();
    }

    /**
     *
     * @return \grunge\system\io\request
     */
    public function getRequest()
    {
        return io\request::getInstance();
    }

}