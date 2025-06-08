<?php

namespace BackendApi\Gateways;

use BackendApi\Contracts\GatewayInterface;
use BackendApi\Exceptions\AppException;

abstract class Gateway implements GatewayInterface {
    protected $config;
    protected $connected = false;
    protected $lastError = null;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function isConnected(): bool {
        return $this->connected;
    }

    public function getLastError(): ?string {
        return $this->lastError;
    }

    protected function setLastError(string $error): void {
        $this->lastError = $error;
    }

    protected function validateConfig(array $required): void {
        foreach ($required as $key) {
            if (!isset($this->config[$key])) {
                throw new AppException("Missing required configuration: $key");
            }
        }
    }

    protected function logError(\Throwable $e): void {
        // Log error to file or monitoring service
        error_log($e->getMessage());
    }

    protected function handleTimeout(): void {
        throw new AppException('Gateway connection timeout');
    }

    protected function handleConnectionError(): void {
        throw new AppException('Failed to connect to gateway');
    }

    protected function handleResponseError(): void {
        throw new AppException('Invalid response from gateway');
    }

    protected function handleAuthenticationError(): void {
        throw new AppException('Authentication failed');
    }

    protected function handleRateLimit(): void {
        throw new AppException('Rate limit exceeded');
    }

    protected function handleServerError(): void {
        throw new AppException('Gateway server error');
    }

    protected function handleNetworkError(): void {
        throw new AppException('Network error occurred');
    }

    protected function handleValidationError(): void {
        throw new AppException('Invalid request data');
    }

    protected function handleUnknownError(): void {
        throw new AppException('An unknown error occurred');
    }
}