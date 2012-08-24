<?php
namespace grunge\system\exceptions;

/**
 * Description of gIoException
 *
 * @author GreeveX <greevex@gmail.com>
 */
class gIoException extends gException {
    public function __construct($message, $code = null, $line = null, $file = null, $trace = null)
    {
        parent::__construct($message, $code = null, $line = null, $file = null, $trace = null);
    }
}