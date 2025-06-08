<?php

namespace BackendApi\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

class Logger {
    protected static $instance = null;
    protected $logger;

    public function __construct() {
        $this->logger = new MonologLogger('app');

        // Add rotating file handler
        $this->logger->pushHandler(
            new RotatingFileHandler(
                storage_path('logs/app.log'),
                30,
                MonologLogger::DEBUG
            )
        );
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function emergency($message, array $context = []) {
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = []) {
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = []) {
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = []) {
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = []) {
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = []) {
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = []) {
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = []) {
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = []) {
        $this->logger->log($level, $message, $context);
    }
}