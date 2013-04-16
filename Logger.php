<?php

namespace pelish8\FisherMan;

class Logger {

    protected static $instance = null;

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
}
