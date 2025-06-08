<?php

namespace BackendApi\Contracts;

interface GatewayInterface {
    public function connect();
    public function disconnect();
    public function isConnected();
    public function send($data);
    public function receive();
    public function handleError(\Throwable $e): void;
}