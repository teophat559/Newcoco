<?php

namespace BackendApi\Adapters;

use BackendApi\Contracts\GatewayInterface;
use BackendApi\Exceptions\AppException;

abstract class Adapter {
    protected $gateway;
    protected $config;

    public function __construct(GatewayInterface $gateway, array $config = []) {
        $this->gateway = $gateway;
        $this->config = $config;
    }

    abstract public function adapt(array $data): array;
    abstract public function reverse(array $data): array;

    protected function validateData(array $data, array $required): void {
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new AppException("Missing required field: $field");
            }
        }
    }

    protected function transformData(array $data, array $mapping): array {
        $result = [];
        foreach ($mapping as $source => $target) {
            if (isset($data[$source])) {
                $result[$target] = $data[$source];
            }
        }
        return $result;
    }

    protected function formatData(array $data, array $formatters): array {
        foreach ($formatters as $field => $formatter) {
            if (isset($data[$field])) {
                $data[$field] = $formatter($data[$field]);
            }
        }
        return $data;
    }

    protected function filterData(array $data, array $allowed): array {
        return array_intersect_key($data, array_flip($allowed));
    }

    protected function mergeData(array $data, array $defaults): array {
        return array_merge($defaults, $data);
    }

    protected function validateResponse(array $response, array $required): void {
        foreach ($required as $field) {
            if (!isset($response[$field])) {
                throw new AppException("Invalid response: missing field $field");
            }
        }
    }

    protected function handleError(\Throwable $e): void {
        throw new AppException($e->getMessage());
    }
}