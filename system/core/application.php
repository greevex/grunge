<?php
namespace grunge\system\core;

abstract class application
{
    abstract public function handle();

    public function run()
    {
        $this->handle();
    }
}