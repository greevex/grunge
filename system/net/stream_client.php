<?php
namespace grunge\system\net;

/**
 * Stream client
 *
 * Date: 21.05.12
 * Time: 16:07
 * @author: Ostrovskiy Grigoriy <greevex@gmail.com>
 */
class stream_client
{
    protected $handle;
    protected $max_bytes_read = 999999999;
    protected $callback;
    protected $delimiter = "\n";
    protected $uri;

    /**
    * Connect to stream uri
    *
    * @param string $uri - [protocol]://[host]:[port]
    * @param array $headers - required only for http(s) connection
    */
    public function connect($uri, $headers = array())
    {
        $this->handle = fopen($uri, 'r', false, stream_context_get_default());
        stream_set_blocking($this->handle, 1);
        if (!$this->handle || !is_resource($this->handle)) {
            throw new \grunge\system\exceptions\gException("Connection failure");
        }
        $this->listen();
    }

    /**
     * When new message received...
     *
     * @param callable $callback
     */
    public function onNewMessage($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Start listen stream
     */
    protected function listen()
    {
        while(!connection_aborted() && !feof($this->handle)) {
            $line = stream_get_line($this->handle, $this->max_bytes_read, $this->getDelimiter());
            call_user_func($this->callback, $line);
        }
    }

    public function __destruct()
    {
        if(is_resource($this->handle)) {
            stream_socket_shutdown($this->handle, STREAM_SHUT_RDWR);
        }
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }
}
