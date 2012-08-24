<?php

namespace grunge\libs\APIWrapper\Driver\Pattern;

/**
 * Class DriverPattern
 */
abstract class DriverPattern
{
    /**
     * Tag constants
     */
    const TAG_RESPONSE          = 'response';
    const TAG_STATUS            = 'status';
    const TAG_DATA              = 'data';

    /**
     * Input method
     * @var string
     */
    protected $method           = '';

    /**
     * Input variables
     * @var array
     */
    protected $input            = array(
        'qs'    => array(), // From query string
        'data'  => array()  // From data
    );

    /**
     * Allowed methods
     * @var array
     */
    static $methods             = array(
        'get', 'post', 'put', 'delete'
    );

    /**
     * Process input string ad return data array
     * @return array
     * @throws \Exception
     */
    public final function input()
    {
        // Get request method
        $this->method = mb_strtolower($_SERVER['REQUEST_METHOD']);

        // Check for method is allowed
        if (!in_array($this->method, self::$methods)) {
            throw new \Exception("Method <{$this->method}> not allowed.");
        }

        // Assign get array to qs
        $this->input['qs'] = $_GET;

        // Return processed data array
        return $this->$this->method();
    }

    /**
     * Get output string
     * @param $status
     * @param array $data
     * @return string
     */
    public final function output($status, array $data)
    {
        // Return output for client
        return $this->prepareOutput($status, $data);
    }

    /**
     *
     * @return string
     */
    public final function getMethod()
    {
        return $this->method;
    }

    /**
     * Return processed data as string
     * @abstract
     * @param $status
     * @param array $data
     * @return string
     */
    protected abstract function prepareOutput($status, array $data);

    /**
     * Process raw data in put and delete
     * @abstract
     * @return mixed
     */
    protected abstract function processInputRawData();

    /**
     * Return processed data as array
     * @abstract
     * @return array
     */
    protected abstract function get();

    /**
     * Return processed data as array
     * @abstract
     * @return array
     */
    protected abstract function post();

    /**
     * Return processed data as array
     * @abstract
     * @return array
     */
    protected abstract function put();

    /**
     * Return processed data as array
     * @abstract
     * @return array
     */
    protected abstract function delete();
}
