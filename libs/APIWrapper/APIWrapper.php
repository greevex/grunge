<?php

namespace grunge\libs\APIWrapper;

class APIWrapper
{
    /**
     * Api driver
     * @var \grunge\libs\APIWrapper\Driver\Pattern\DriverPattern
     */
    private $driver             = '';

    /**
     * Allowed drivers
     * @var array
     */
    private static $drivers     = array(
        'Standard', 'Json', 'XML'
    );

    /**
     * Создание драйвера в конструкторе
     * @param string $driver
     * @throws \Exception
     */
    public function __construct($driver = 'Standard')
    {
        // If method not exists -> Bye baby, bye, bye
        if (!in_array($driver, self::$drivers)) {
            throw new \Exception("Api wrapper driver <{$driver}> not found.");
        }

        // Assign new class name
        $className      = "{$this->driver}Driver";

        // Driver
        $this->driver   = new $className;
    }

    public function APIMethod()
    {
        return $this->driver->getMethod();
    }

    /**
     * Обработка входящего запроса
     * @return array
     */
    public function APIInput()
    {
        return $this->driver->input();
    }

    /**
     * Формирование строки ответа
     * @param $status
     * @param array $data
     * @return string
     */
    public function APIOutput($status, array $data)
    {
        return $this->driver->output($status, $data);
    }
}
