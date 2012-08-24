<?php

namespace grunge\libs\APIWrapper\Driver;

/**
 * Class JsonDriver
 */
class JsonDriver
    extends StandardDriver
{
    /**
     * Json decode wrapper
     * @url http://php.net/manual/function.json-decode.php
     * @see json_decode
     * @throws \Exception
     */
    protected function processInputRawData()
    {
        if (!($this->input['data'] = json_decode(@file_get_contents('php://input'), true))) {
            throw new \Exception("Could not process input data.");
        }
    }
}
