<?php

namespace BackendApi\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    protected static $instance = null;
    protected $db;
    protected $key;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->key = Config::get('key');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function attempt($email, $password) {
        $user = $this->db->fetch(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        return $this->login($user);
    }

    public function login($user) {
        $token = $this->createToken($user);
        Session::getInstance()->set('user', $user);
        Session::getInstance()->set('token', $token);
        return $token;
    }

    public function logout() {
        Session::getInstance()->remove('user');
        Session::getInstance()->remove('token');
    }

    public function user() {
        return Session::getInstance()->get('user');
    }

    public function check() {
        return Session::getInstance()->has('user');
    }

    public function createToken($user) {
        $payload = [
            'iss' => Config::get('url'),
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 hours
            'user' => [
                'id' => $user['id'],
                'email' => $user['email']
            ]
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}