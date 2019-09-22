<?php
class Log {
    const LOG_DIR = 'log';
    
    var $path;
    var $instance;
    var $fopen;

    private function __construct() {
        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 755, true);
        }
        $this->path = self::LOG_DIR.'/'.time().'.log';
        $this->fopen = fopen($this->path, 'ab+');
    }

    function __destruct() {
        fclose($this->fopen);
    }

    function i($message) {
        fwrite($this->fopen, $message);
    }

    static function getInstance() {
        if (!isset($instance)) {
            $instance = new Log();
        }
        return $instance;
    }
}


function logi($message) {
    $log = Log::getInstance();
    $log->i($message);
}
