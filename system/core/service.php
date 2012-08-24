<?php
namespace grunge\system\core;

\grunge\system\service\fileLoader::load(GRUNGE_PATH . '/libs/Pear/System/Daemon.php');

abstract class service
extends \grunge\system\modules\simple\simpleHelper
{
    private $options = array(
        'appName' => '',
        'appDescription' => '',
        'appDir' => '',
        'authorName' => 'root',
        'authorEmail' => 'root@localhost',
        'sysMaxExecutionTime' => '0',
        'sysMaxInputTime' => '0',
        'sysMemoryLimit' => '1024M',
        'appPidLocation' => '/tmp/mydaemon.pid',
        'logLocation' => '/tmp/mydaemon.log'
    );

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        if(isset($this->options[$key])) {
            $this->options[$key] = $value;
            return true;
        }
        return false;
    }

    public function initOptions()
    {
        \System_Daemon::setOptions($this->options);
    }

    public function __construct()
    {
        $this->options['appDir'] = dirname(__FILE__);
        parent::__construct();
    }

    public function status()
    {
        $pid = \System_Daemon::isRunning();
        if($pid) {
            $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Status: running: {$pid}");
        } else {
            $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Status: stopped");
        }
    }

    public function getPid()
    {
        return \System_Daemon::isRunning();
    }

    public function stop()
    {
        $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Stopping...");
        \System_Daemon::stop();
        $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Stopped!");

    }

    abstract protected function setup();

    public function start()
    {
        $this->setup();
        $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Starting...");
        if(empty($this->options['appName']) && empty($this->options['appDir'])) {
            $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Can't run: appName or/and appDir is not set!");
            return false;
        }
        \System_Daemon::start();
        $this->response->writeLn("[" . \System_Daemon::getOption('appName') . "] Started!");
        $this->handle();
    }
}
