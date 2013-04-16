<?php

namespace pelish8\FisherMan;

class Logger {

    protected static $instance = null;

    protected $path = null;

    protected function __construct()
    {

    }

    public static sharedLogger()
    {
        if (static::instance === null) {
            static::instance = new static();
        }

        return static::instance;
    }

    public function test()
    {
        echo 'Test logger!';
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function path()
    {
        if ($this->path === null) {
            $this->path = 
        }

        return $this->path;
    }
}
