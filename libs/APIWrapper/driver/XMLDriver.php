<?php

namespace grunge\libs\APIWrapper\Driver;


class XMLDriver
    extends StandardDriver
{

    /**
     * XML decode wrapper
     * @throws \Exception
     */
    protected function processInputRawData()
    {
        throw new \Exception("Not implemented yet.");

        $string     = @file_get_contents('php://input');
        $DOM        = new \DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);

        if (!$string) {
            throw new \Exception("Empty input string.");
        }

        $DOM->loadXML($string);
        libxml_clear_errors();



        if (!($this->input['data'] = json_decode(@file_get_contents('php://input'), true))) {
            throw new \Exception("Could not process input data.");
        }
    }

    /**
     * Return json encoded output
     * @param $status
     * @param array $data
     * @return string
     */
    protected function prepareOutput($status, array $data)
    {
        $error = isset($data['error']) ? $data['error'] : '';

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><response><status>{$status}</status><error>{$error}</error><data/></response>";

    }

}
