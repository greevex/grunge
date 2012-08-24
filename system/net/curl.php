<?php

namespace grunge\system\net;

/**
 * Curl driver for HTTP Request class
 *
 * @author GreeveX <greevex@gmail.com>
 */
class curl
{
    private $curl;

    private $_defaultoptions = array(
        CURLOPT_VERBOSE             => 0,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_FOLLOWLOCATION      => 10,
        CURLOPT_SSL_VERIFYPEER      => false,
        CURLOPT_USERAGENT           => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16',
        CURLOPT_CONNECTTIMEOUT      => 30,
        CURLOPT_TIMEOUT             => 90,
        CURLOPT_CUSTOMREQUEST       => 'GET',
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_0,
        CURLOPT_COOKIEJAR           => '/tmp/global_cookie.sds',
        CURLOPT_COOKIEFILE          => '/tmp/global_cookie.sds',
    );

    public function setVerbose($verbose = 1)
    {
        $this->options[CURLOPT_VERBOSE] = $verbose;
    }

    private $options = array();

    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function reset()
    {
        $this->curl = curl_init();
        $this->options = $this->_defaultoptions;
    }

    public function setCookieFile($path)
    {
        $this->options[CURLOPT_COOKIEJAR] = $path;
        $this->options[CURLOPT_COOKIEFILE] = $path;
    }

    public function selectInterface($interface)
    {
        $this->options[CURLOPT_INTERFACE] = $interface;
    }

    public function prepare($url, $params = null, $method = null)
    {
        if($method === null) {
            $method = 'GET';
        }
        $url = trim($url);
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        switch(strtoupper($method)) {
            case 'GET':
                $this->options[CURLOPT_CUSTOMREQUEST] = 'GET';
                if($params !== null) {
                    $url .= strpos($url, '?') === false ? "?$params" : "&$params";
                }
                break;
            case 'PUT':
                $this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                if($params !== null) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'DELETE':
                $this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if($params !== null) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'POST':
                $this->options[CURLOPT_CUSTOMREQUEST] = 'POST';
                if($params !== null) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
                }
                curl_setopt($this->curl, CURLOPT_POST, 1);
                break;
        }

        $this->options[CURLOPT_URL] = $url;

        curl_setopt_array($this->curl, $this->options);

        return $this->curl;
    }

    public function addOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function execute()
    {
        $result = curl_exec($this->curl);

        if (($curl_errno = curl_errno($this->curl)) != CURLE_OK) {
            $curl_error = curl_error($this->curl);
            if(strpos($curl_error, 'bind') !== false) {
                $this->selectInterface("venet0:0");
                $result = $this->execute();
            } else {
                curl_close($this->curl);
                $this->curl = null;
                throw new CurlException($curl_error, $curl_errno);
            }
        }

        return $result;
    }

    public function setConnectTimeout($seconds)
    {
        $this->options[CURLOPT_CONNECTTIMEOUT] = intval($seconds);
    }

    public function setTimeout($seconds)
    {
        $this->options[CURLOPT_TIMEOUT] = intval($seconds);
    }
}

class CurlException extends \Exception {}
