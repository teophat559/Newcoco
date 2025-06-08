<?php

namespace BackendApi\Exceptions;

class AppException extends \Exception {
    protected $context = [];

    public function __construct(string $message = "", array $context = [], int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array {
        return $this->context;
    }

    public function setContext(array $context): void {
        $this->context = $context;
    }

    public function addContext(string $key, $value): void {
        $this->context[$key] = $value;
    }

    public function getContextValue(string $key, $default = null) {
        return $this->context[$key] ?? $default;
    }

    public function hasContext(string $key): bool {
        return isset($this->context[$key]);
    }

    public function removeContext(string $key): void {
        unset($this->context[$key]);
    }

    public function clearContext(): void {
        $this->context = [];
    }
}