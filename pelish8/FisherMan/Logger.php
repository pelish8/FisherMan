<?php

namespace pelish8\FisherMan;

/* FisherMan
 *
 * @package FisherMan
 * @author  pelish8
 * @since   0.1
 */
class Logger {

    protected static $instance = null;

    protected $path = null;


    protected $logLevel = 'ERROR';

    protected function __construct()
    {
        if ($this->path === null) {
            $this->path = ini_get('error_log');
        }

// error_log("Oracle database not available!", 0);

        if (!is_writable($this->path)) {
            echo 'The log file is not writable';
        }
    }

    public static function sharedLogger()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    protected function errorLog($msg)
    {
        error_log('[' . $this->logLevel . '] ' . $msg, 0);
    }

}
