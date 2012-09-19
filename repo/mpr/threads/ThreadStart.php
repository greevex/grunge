<?php
namespace mpr\threads;

/**
 * Thread wrapper
 *
 */
class ThreadStart {

    private $callable;

    private $thread;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function setThread($thread)
    {
        $this->thread = $thread;
    }

    public function call()
    {
        $arguments = func_get_args();
        $result = call_user_func_array($this->callable, $arguments);
        $this->thread->setResult($result);
    }

}

// EOF