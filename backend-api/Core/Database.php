<?php

namespace BackendApi\Core;

use PDO;
use PDOException;

class Database {
    protected static $instance = null;
    protected $pdo;

    public function __construct() {
        $config = Config::get('services.database');

        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $this->query($sql, $values);
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $fields = array_keys($data);
        $values = array_values($data);
        $set = array_map(function($field) {
            return "$field = ?";
        }, $fields);

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $set),
            $where
        );

        return $this->query($sql, array_merge($values, $whereParams))->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        return $this->query($sql, $params)->rowCount();
    }
}