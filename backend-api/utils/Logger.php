<?php

namespace BackendApi\Utils;

class Logger {
    private static $instance = null;
    private $logDir;
    private $logLevel;
    private $logFormat;

    private function __construct() {
        $this->logDir = LOG_DIR;
        $this->logLevel = LOG_LEVEL;
        $this->logFormat = LOG_FORMAT;

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function debug($message, array $context = []) {
        $this->log('debug', $message, $context);
    }

    public function info($message, array $context = []) {
        $this->log('info', $message, $context);
    }

    public function warning($message, array $context = []) {
        $this->log('warning', $message, $context);
    }

    public function error($message, array $context = []) {
        $this->log('error', $message, $context);
    }

    public function critical($message, array $context = []) {
        $this->log('critical', $message, $context);
    }

    private function log($level, $message, array $context = []) {
        if (!$this->shouldLog($level)) {
            return;
        }

        $logFile = $this->getLogFile($level);
        $logMessage = $this->formatMessage($level, $message, $context);

        $this->writeLog($logFile, $logMessage);
        $this->rotateLog($logFile);
    }

    private function shouldLog($level) {
        $levels = [
            'debug' => 0,
            'info' => 1,
            'warning' => 2,
            'error' => 3,
            'critical' => 4
        ];

        return $levels[$level] >= $levels[$this->logLevel];
    }

    private function getLogFile($level) {
        switch ($level) {
            case 'error':
                return $this->logDir . LOG_ERROR_FILE;
            case 'debug':
                return $this->logDir . LOG_DEBUG_FILE;
            default:
                return $this->logDir . LOG_ACCESS_FILE;
        }
    }

    private function formatMessage($level, $message, array $context = []) {
        $replace = [
            '%datetime%' => date(DATETIME_FORMAT),
            '%level%' => strtoupper($level),
            '%message%' => $message,
            '%context%' => !empty($context) ? json_encode($context) : '',
            '%extra%' => ''
        ];

        return strtr($this->logFormat, $replace);
    }

    private function writeLog($file, $message) {
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
    }

    private function rotateLog($file) {
        if (!file_exists($file)) {
            return;
        }

        if (filesize($file) < LOG_MAX_SIZE) {
            return;
        }

        for ($i = LOG_MAX_FILES - 1; $i >= 1; $i--) {
            $oldFile = $file . '.' . $i;
            $newFile = $file . '.' . ($i + 1);

            if (file_exists($oldFile)) {
                if ($i === LOG_MAX_FILES - 1) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }

        rename($file, $file . '.1');
    }
}