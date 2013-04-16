<?php

namespace pelish8\FisherMan;

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
    protected $defaultLogPath = 'default/Path';

    /**
     *
     *
     */
    protected $ip = null;

    /**
     *
     *
     */
    protected $method = null;

    /**
     *
     *
     */
    protected $route = null; // uri

    /**
     *
     *
     */
    protected function __construt()
    {
    }

    /**
     *
     *
     */
    public static function sharedEnvironment()
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
    public function route() // uri
    {
        return $this->route; // uri
    }
}
