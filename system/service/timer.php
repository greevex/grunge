<?php
namespace grunge\system\service;

/**
 * Description of timer
 *
 * @author GreeveX <greevex@gmail.com>
 */
class timer {

    private $time_start;

    private $time_end;

    public function __construct()
    {
        \grunge\system\debug\debug::put(
                "Timer initialized...", __METHOD__, 10);
    }

    public function start()
    {
        \grunge\system\debug\debug::put(
                "Timer start...", __METHOD__, 10);
        $this->time_start = microtime(true);
        return $this;
    }

    public function stop()
    {
        \grunge\system\debug\debug::put(
                "Timer stop...", __METHOD__, 10);
        $this->time_end = microtime(true);
        return $this;
    }

    public function getPeriod($precision = 4)
    {
        $this->time_end = microtime(true);
        \grunge\system\debug\debug::put(
                "Getting interval", __METHOD__, 10);
        return round($this->time_end - $this->time_start, $precision);
    }

    public function reset()
    {
        $this->time_start = 0;
        $this->time_end = 0;
        return $this;
    }
}