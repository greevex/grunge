<?php

namespace grunge\system\exceptions;

class gException
extends \Exception
{

    /**
     * Exception name
     *
     * @var string
     */
    private $name;

    /**
     * Exception line
     *
     * @var int
     */
    protected $line;

    /**
     * Exception file
     *
     * @var string
     */
    protected $file;

    /**
     * Exception trace
     *
     * @var mixed
     */
    protected $trace;

    /**
     * Construct
     *
     * @param string $message сообщение исключения
     * @param integer $code код исключения
     * @param string $line строка исключения
     * @param string $file файл исключения
     * @param array $prev_trace trace от предыдущего исключения
     */
    public function __construct($message, $code = null, $line = null, $file = null, $trace = null) {
        $this->name = 'Grunge System Exception';

        if ($line) {
            $this->line = $line;
        }
        if ($file) {
            $this->file = $file;
        }

        $this->trace = $trace;
        $error_message = "* * * [ GRUNGE ERROR ] * * *\n";
        $error_message .= "{$message} in {$file}:{$line}\n";
        $error_message .= $this->readFile($file, $line);
        $error_message .= $this->getDebugBacktrace(10);
        parent::__construct($message, $code);
    }

    private function getDebugBacktrace($limit = 1024)
    {
        $string = "";
        $counter = 0;
        ob_start();
        debug_print_backtrace();
        $string = ob_get_contents();
        ob_end_clean();
        return $string;
    }

    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine) {
        print "Exception: {$errorMessage} in {$errorFile}:{$errorLine}\n";
        throw new self($errorMessage, $errorNumber, $errorLine, $errorFile);
    }

    private function getLinesAround($total, $current, $around = 2) {
        $return = array('min' => $current, 'max' => $current);
        for ($i = $current; $i >= ($current - $around) && $i > 0; $i--) {
            $return['min'] = $i;
        }
        for ($i = $current; $i <= ($current + $around) && $i <= $total; $i++) {
            $return['max'] = $i;
        }
        return $return;
    }

    private function readFile($file, $line) {
        if(empty($file) || !file_exists($file)) {
            return false;
        }
        $string = "";
        $file = file($file);
        $lines = $this->getLinesAround(count($file), $line, 2);
        for ($i = $lines['min'] - 1; $i < $lines['max']; $i++) {
            $line = $i + 1;
            $string .= "[{$line}] {$file[$i]}";
        }
        return $string;
    }

}