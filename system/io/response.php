<?php
namespace grunge\system\io;

use grunge\system\systemConfig;

/**
 * Description of response
 *
 * @author GreeveX <greevex@gmail.com>
 */
class response {

    private static $response = array();

    private $outputResource;

    private $output_buffering = false;

    private $output_filepath;

    private $buffer = "";

    public function __construct($type = null, $buffering = null, $filepath = null)
    {
        if($type == null) {
            $type = systemConfig::$output_type;
        }
        if($buffering !== null) {
            $this->output_buffering = $buffering;
        } else {
            $this->output_buffering = systemConfig::$output_buffering;
        }
        switch($type) {
            case 'file':
                if($filepath != null) {
                    $this->setOutputFilepath($filepath);
                } else {
                    $this->setOutputFilepath(systemConfig::$output_filepath);
                }
                $this->outputResource = fopen($this->output_filepath, 'w');
                break;
            case 'output':
                $this->outputResource = fopen('php://output', 'w');
                break;
            case 'stdout':
            default:
                $this->outputResource = fopen('php://stdout', 'w');
                break;
        }
    }

    /**
     * @static
     * @return array|response
     */
    public static function getAllInstances()
    {
        return self::$response;
    }

    public static function closeAll()
    {
        foreach(self::$response as $response) {
            $response->close();
        }
        self::$response = [];
    }

    /**
     *
     *
     * @return response
     */
    public static function getInstance($name = 'default')
    {
        \grunge\system\debug\debug::put(
            "self::\$response[{$name}] = " . (isset(self::$response[$name]) ? 'null' : 'loaded'), __METHOD__, 5);
        if(!isset(self::$response[$name]) || isset(self::$response[$name]) && self::$response[$name] == null) {
            self::$response[$name] = new self();
        }
        return self::$response[$name];
    }

    public function commit()
    {
        \grunge\system\debug\debug::put(
            '$this->output_buffering length = ' . mb_strlen($this->buffer), __METHOD__, 5);
        if($this->output_buffering) {
            fwrite($this->outputResource, $this->buffer);
            $this->buffer = "";
        }
    }

    public function write($string)
    {
        if($this->output_buffering) {
            $this->buffer .= $string;
        } else {
            if(!is_resource($this->outputResource)) {
                return $this;
            }
            fwrite($this->outputResource, $string);
        }
        return $this;
    }

    /**
     * @param $string
     * @return response
     */
    public function writeLn($string)
    {
        $this->write($string . "\n");
        return $this;
    }

    /**
    * Outputs exception info
    *
    * @param \Exception $exception
    * @return response
    */
    public function writeException($exception)
    {
        $this->write("EXCEPTION: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}" . "\n");
        return $this;
    }

    public function setOutputResource($resource)
    {
        if(is_resource($this->outputResource)) {
            fclose($this->outputResource);
        }
        $this->outputResource = $resource;
    }

    /**
     * @return resource
     */
    public function getOutputResource()
    {
        return $this->outputResource;
    }

    public function getOutputFilepath()
    {
        \grunge\system\debug\debug::put(
            "Output filepath requested", __METHOD__, 5);
        return $this->output_filepath;
    }

    public function setOutputFilepath($output_filepath)
    {
        if(\grunge\system\debug\debug::isInitialized()) {
            \grunge\system\debug\debug::put(
                "output_filepath:{$output_filepath}", __METHOD__, 5);
        }
        $this->output_filepath = $output_filepath;
    }

    public function close()
    {
        \grunge\system\debug\debug::put("Closing resource...", __METHOD__, 5);
        $this->commit();
        if(is_resource($this->outputResource)) {
            fclose($this->outputResource);
        }
    }

    public function __destruct()
    {
        \grunge\system\debug\debug::put(
            "Destructing...", __METHOD__, 5);
        $this->close();
    }
}