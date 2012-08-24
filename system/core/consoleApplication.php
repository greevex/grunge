<?php
namespace grunge\system\core;

if(PHP_SAPI != 'cli') {

    if(!defined('SIGTERM')) {
        define('SIGTERM', 15);
        define('SIGKILL', 9);
    }
}
/**
 * Description of consoleApplication
 *
 * @author GreeveX <greevex@gmail.com>
 */
abstract class consoleApplication
extends \grunge\system\modules\simple\simpleHelper
{

    /**
    * Function to parse php arguments
	*
	* @param array $argv
	* @return array
	*/
    public function importArgs($argv){
        array_shift($argv);
        $out = array();
        foreach ($argv as $arg){
            if (substr($arg,0,2) == '--'){
                $eqPos = strpos($arg,'=');
                if ($eqPos === false){
                    $key = substr($arg,2);
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                } else {
                    $key = substr($arg,2,$eqPos-2);
                    $out[$key] = substr($arg,$eqPos+1);
                }
            } elseif (substr($arg,0,1) == '-'){
                if (substr($arg,2,1) == '='){
                    $key = substr($arg,1,1);
                    $out[$key] = substr($arg,3);
                } else {
                    $chars = str_split(substr($arg,1));
                    foreach ($chars as $char){
                        $key = $char;
                        $out[$key] = isset($out[$key]) ? $out[$key] : true;
                    }
                }
            } elseif (substr($arg,0,1) == ']'){
                list($key, $value) = explode('=', $arg);
                $out[$key] = $value;
            } else {
                $out[] = $arg;
            }
        }
        $this->argv = $out;
        return $this;
    }

    public function setArgv($argv)
    {
        $this->argv = $argv;
    }

    protected $argv = array();

    protected $daemonized = false;

    public function __construct()
    {
        if(strtolower(php_sapi_name()) == 'cli') {
            pcntl_signal(SIGTERM, array($this, 'shutdown'));
        }
        register_shutdown_function(array($this, 'shutdown'));
        parent::__construct();
        if(strtolower(php_sapi_name()) == 'cli') {
            pcntl_signal_dispatch();
        }
    }

    public function shutdown($signo = SIGTERM)
    {
        exit();
    }

    public function daemonize()
    {
        $this->daemonized = false;
        static $STDIN, $STDOUT, $STDERR;

        $this->output->writeLn("Daemonizing...");

        if(strtolower(php_sapi_name()) != 'cli') {
            throw new \Exception("Can't daemonize in non-cli sapi");
        }
        $pid = pcntl_fork();

        if ($pid < 0) { // Fail
            $this->output->writeLn("Daemonization failed!");
            exit();
        } elseif ($pid > 0) { // Parent
            $this->output->writeLn("Daemonized! PID:{$pid}");
            exit();
        } // Child

        \grunge\system\io\response::closeAll();
        $baseDir = dirname(__FILE__);

        posix_setsid();

        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
        $STDIN = fopen('/dev/null', 'r');
        $STDOUT = fopen(\grunge\system\systemConfig::$pathToTemp . '/log.application.' . getmypid(), 'ab');
        $STDERR = fopen(\grunge\system\systemConfig::$pathToTemp . '/log.error.' . getmypid(), 'ab');

        $this->daemonized = true;
    }

    abstract public function handle();

    public $app_config;

    public $only_one_instance = false;

    public function run()
    {
        if(isset($this->argv['daemonize'])) {
            unset($this->argv['daemonize']);
            $this->daemonize();
        }
        if(count($this->argv)) {
            $this->request->setArguments($this->argv);
        }
        if(isset($this->argv['app'])) {
            $app_name = $this->argv['app'];
            unset($this->argv['app']);
            if(count($_REQUEST)) {
                $this->request->importArguments(\grunge\system\io\request::SC_REQUEST);
            }
            if(!empty($app_name)) {
                $full_app_name = "\\grunge\\bin\\{$app_name}";
                try {
                    $loaded = \grunge\system\service\fileLoader::load($full_app_name);
                } catch(\grunge\system\exceptions\gException $e) {
                    $loaded = false;
                }
                if(!$loaded || !class_exists($full_app_name)) {
                    $this->output->writeLn("Application «{$app_name}» doesn't exists!");
                    return;
                }

                $app = new $full_app_name();
                $app->setArgv($this->argv);
                $app->run();
                return;
            }
        }
        \grunge\system\debug\debug::put("Starting application...", __FILE__);
        if(!$this->only_one_instance || $this->canRun()) {
            $this->handle();
        } else {
            \grunge\system\debug\debug::put("Only one instance allowed!", __FILE__);
        }
        \grunge\system\debug\debug::put("Ending application...", __FILE__);
    }

    private $creater = false;

    private function isPidAlive($pid)
    {
        if(!function_exists('posix_getsid')) {
            if(strtolower(PHP_OS) == 'linux') {
                if(is_dir('/proc') && count(scandir('/proc')) > 2) {
                    if(file_exists("/proc/{$pid}")) {
                        return 1;
                    }
                }
            }
            $exists = exec("ps ax | grep -v grep | awk '{print $1}' | egrep '^{$pid}$'");
            if($exists == $pid) {
                return 1;
            }
            return 0;
        }
        return (posix_getsid($pid) !== false) ? 1 : 0;
    }

    private function canRun()
    {
        $lockfile = __DIR__ . '/' . md5(\grunge\system\systemConfig::$application_name) . '.lock';
        if(file_exists($lockfile)) {
            $pid = file_get_contents($lockfile);
            if ($this->isPidAlive($pid)) {
                return 0;
            }
        }
        file_put_contents($lockfile, getmypid());
        $this->creater = true;
        return 1;
    }

    public function __destruct()
    {
        if($this->creater) {
            $lockfile = __DIR__ . '/' . md5(\grunge\system\systemConfig::$application_name) . '.lock';
            if(file_exists($lockfile) && !unlink($lockfile)) {
                exec("rm -f '{$lockfile}'");
            }
        }
    }
}
