<?php

namespace grunge\libs\APIWrapper\Driver;


class StandardDriver
    extends \grunge\libs\APIWrapper\Driver\Pattern\DriverPattern
{

    /**
     * Get method implementation
     * @return array
     */
    protected function get()
    {
        return $this->input;
    }

    /**
     * Post method implementation
     * @return array
     */
    protected function post()
    {
        $this->processInputRawData();

        return $this->input;
    }

    /**
     * Put method implementation
     * @return array
     */
    protected function put()
    {
        $this->processInputRawData();

        return $this->input;
    }

    /**
     * Delete method implementation
     * @return array
     */
    protected function delete()
    {
        return $this->input;
    }

    protected function processInputRawData()
    {
        $raw = array();
        foreach(explode('&', file_get_contents('php://input')) as $pair) {
            $item = explode('=', $pair);
            if(count($item) == 2) {
                $raw[urldecode($item[0])] = urldecode($item[1]);
            }
        }

        $this->input['data'] = $raw;
    }

    /**
     * Return json encoded output
     * @param $status
     * @param array $data
     * @return string
     */
    protected function prepareOutput($status, array $data)
    {
        return json_encode(
            array(
                'response'  => array(
                    'status'    => $status,
                    'data'      => $data
                )
            )
        );
    }
}
