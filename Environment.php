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

        if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0) {
            $this->uri = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'; // home
        } else {
            $this->uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/'; // mode_rewrite
        }

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
}
