<?php
namespace grunge\system\threads;

/**
 * Implements threading in PHP
 *
 */
class Thread {

    const FUNCTION_NOT_CALLABLE     = 10;
    const COULD_NOT_FORK            = 15;
    const FORK_READY                = -50;

    public function getPath()
    {
        $pid = $this->getPid();
        if(empty($pid)) {
            $pid = getmypid();
        }
        return sys_get_temp_dir() . "/phpthread.{$pid}.data";
    }

    /**
     * possible errors
     *
     * @var array
     */
    private $errors = array(
        Thread::FUNCTION_NOT_CALLABLE   => 'You must specify a valid function name that can be called from the current scope.',
        Thread::COULD_NOT_FORK          => 'pcntl_fork() returned a status of -1. No new process was created',
    );

    /**
     * callback for the function that should
     * run as a separate thread
     *
     * @var callback
     */
    protected $runnable;

    protected $object;

    /**
     * holds the current process id
     *
     * @var integer
     */
    private $pid;

    /**
     * checks if threading is supported by the current
     * PHP configuration
     *
     * @return boolean
     */
    public static function available() {
        $required_functions = array(
            'pcntl_fork',
        );

        foreach( $required_functions as $function ) {
            if ( !function_exists( $function ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * class constructor - you can pass
     * the callback function as an argument
     *
     * @param ThreadStart $_threadStart
     */
    public function __construct( $_threadStart = null )
    {
        if( $_threadStart !== null ) {
            $this->setRunnable( $_threadStart );
        }
    }

    public function setResult($data)
    {
        file_put_contents($this->getPath(), serialize($data));
    }

    public function getResult()
    {
        if(!file_exists($this->getPath())) {
            return;
        }
        $data = unserialize(file_get_contents($this->getPath()));
        unlink($this->getPath());
        return $data;
    }

    public function setRunnable($runnable) {
        $this->runnable = $runnable;
    }

    /**
     * gets the callback
     *
     * @return callback
     */
    public function getRunnable() {
        return $this->runnable;
    }

    /**
     * returns the process id (pid) of the simulated thread
     *
     * @return int
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * checks if the child thread is alive
     *
     * @return boolean
     */
    public function isAlive() {
        $pid = pcntl_waitpid( $this->pid, $status, WNOHANG );
        return ( $pid === 0 );
    }

    /**
     * starts the thread, all the parameters are
     * passed to the callback function
     *
     * @return void
     */
    public function start() {
        $pid = @ pcntl_fork();
        if( $pid == -1 ) {
            throw new \Exception( $this->getError( Thread::COULD_NOT_FORK ), Thread::COULD_NOT_FORK );
        }
        if( $pid ) {
            // parent
            $this->pid = $pid;
            $status = null;
            //pcntl_waitpid($pid, $status);
        }
        else {
            // child
            $this->runnable->setThread($this);
            $arguments = func_get_args();
            pcntl_signal(SIGTERM, array($this, 'signalHandler'));
            register_shutdown_function(array($this, 'signalHandler'));
            pcntl_signal_dispatch();
            call_user_func_array(array($this->runnable, 'call'), $arguments);
            //posix_kill($this->pid, self::FORK_READY);
            exit(0);
        }
        return $this;
    }

    /**
     * attempts to stop the thread
     * returns true on success and false otherwise
     *
     * @param integer $_signal - SIGKILL/SIGTERM
     * @param boolean $_wait
     */
    public function stop( $_signal = SIGKILL, $_wait = false ) {
        $isAlive = (int)$this->isAlive();
        $this->output->writeLn("Stopping process {$this->pid}, alive:{$isAlive}");
        if($isAlive) {
            posix_kill( $this->pid, $_signal );
            if( $_wait ) {
                pcntl_waitpid( $this->pid, $status = 0 );
            }
        }
    }

    /**
     * alias of stop();
     *
     * @return boolean
     */
    public function kill( $_signal = SIGKILL, $_wait = false ) {
        echo "Killing process with pid {$this->pid}...\n";
        for($i = 0; $i < 10; $i++) {
            posix_kill( $this->pid, $_signal );
            usleep(10000);
        }
        if( $_wait ) {
            echo "Waiting process [pid {$this->pid}]...\n";
            pcntl_waitpid( $this->pid, $status = 0 );
        }
        echo "Killed! [pid {$this->pid}]...\n";
    }

    /**
     * gets the error's message based on
     * its id
     *
     * @param integer $_code
     * @return string
     */
    public function getError( $_code ) {
        if ( isset( $this->errors[$_code] ) ) {
            return $this->errors[$_code];
        }
        else {
            return 'No such error code ' . $_code . '! Quit inventing errors!!!';
        }
    }

    /**
     * signal handler
     *
     * @param integer $_signal
     */
    protected function signalHandler($_signal = SIGTERM) {
        switch($_signal) {
            case SIGTERM:
                print __METHOD__ . ":exit()\n";
                exit();
                break;
        }
    }
}

// EOF