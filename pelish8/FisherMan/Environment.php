<?php

namespace pelish8\FisherMan;

/**
 * FisherMan
 *
 * @package FisherMan
 * @author  pelish8
 * @since   0.1
 */
class Environment
{

    /**
     *
     *
     */
    protected static $instance = null;

    /**
     *
     *
     */
    protected $defaultLogPath = '';

    /**
     *
     *
     */
    protected $ip = null;

    /**
     *
     *
     */
    protected $port = null;

    /**
     *
     *
     */
    protected $method = null;

    /**
     *
     *
     */
    protected $uri = null; // uri

    /**
     *
     *
     */
    protected $host = null; // uri

    /**
     *
     *
     */
    protected $serverName = null; // uri

    /**
     *
     *
     */
    protected function __construct()
    {
        // set method
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->uri = $this->prepareUri();

        $this->ip = $_SERVER['REMOTE_ADDR'];

        $this->port = $_SERVER['REMOTE_PORT'];

        $this->host = $_SERVER['HTTP_HOST'];

        $this->serverName = $_SERVER['SERVER_NAME'];
    }

    /**
     *
     *
     */
    public static function sharedEnvironment($root = null)
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     *
     *
     */
    protected function prepareUri()
    {
        if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = @$_SERVER['PATH_INFO']; //@todo better implementation
        } else {
            $uri = $_SERVER['REQUEST_URI']; // mode_rewrite
        }

        $len = strlen($uri);

        if ($uri === '/' || $len === 0) { // @todo test with mod_rewrite
            return '/';
        }

        if (substr($uri, -1) === '/') {
            return substr($uri, 0, $len - 1);
        }

        return $uri;
    }

    /**
     *
     *
     */
    public function logPath()
    {
        return $this->defaultLogPath;
    }

    /**
     *
     *
     */
    public function method()
    {
        return $this->method;
    }

    /**
     *
     *
     */
    public function ip()
    {
        return $this->ip;
    }

    /**
     *
     *
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     *
     *
     */
    public function port()
    {
        return $this->port;
    }

    /**
     *
     *
     */
    public function host()
    {
        return $this->host;
    }

    /**
     *
     *
     */
    public function serverName()
    {
        return $this->serverName;
    }

    /**
     *
     *
     */
    public static function mock($array)
    {
        $inc = new static();

        $inc->ip = $array['ip'];

        $inc->uri = $array['uri'];

        $inc->host = $array['host'];

        $inc->port = $array['port'];

        $inc->serverName = $array['serverName'];

        $inc->method = $array['method'];

        static::$instance = $inc;

        return static::$instance;
    }
}
