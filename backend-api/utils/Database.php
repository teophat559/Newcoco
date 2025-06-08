<?php

namespace BackendApi\Utils;

use BackendApi\Exceptions\AppException;
use PDO;
use PDOException;

class Database extends PDO {
    private static $instance = null;
    private $inTransaction = false;
    private $retryCount = 0;

    public function __construct($dsn, $username = null, $password = null, $options = []) {
        try {
            parent::__construct($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new AppException('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }
        return self::$instance;
    }

    public function fetch($query, $params = []) {
        try {
            $stmt = $this->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function fetchAll($query, $params = []) {
        try {
            $stmt = $this->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function executeQuery($query, $params = []) {
        try {
            $stmt = $this->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function insert($table, array $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';

        $query = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES ($placeholders)";

        try {
            $stmt = $this->prepare($query);
            $stmt->execute($values);
            return $this->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function update($table, array $data, $where, array $whereParams = []) {
        $fields = array_keys($data);
        $values = array_values($data);
        $set = implode('=?,', $fields) . '=?';

        $query = "UPDATE {$table} SET {$set} WHERE {$where}";

        try {
            $stmt = $this->prepare($query);
            $stmt->execute(array_merge($values, $whereParams));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function delete($table, $where, array $params = []) {
        $query = "DELETE FROM {$table} WHERE {$where}";

        try {
            $stmt = $this->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    public function beginTransaction() {
        if (!$this->inTransaction) {
            $this->inTransaction = parent::beginTransaction();
        }
        return $this->inTransaction;
    }

    public function commit() {
        if ($this->inTransaction) {
            $this->inTransaction = !parent::commit();
        }
        return !$this->inTransaction;
    }

    public function rollback() {
        if ($this->inTransaction) {
            $this->inTransaction = !parent::rollback();
        }
        return !$this->inTransaction;
    }

    public function inTransaction() {
        return $this->inTransaction;
    }

    private function handleError(PDOException $e) {
        if ($this->retryCount < DB_MAX_RETRIES) {
            $this->retryCount++;
            sleep(DB_RETRY_DELAY);
            return $this->retry();
        }
        throw new AppException('Database error: ' . $e->getMessage());
    }

    private function retry() {
        try {
            $this->query('SELECT 1');
            $this->retryCount = 0;
            return true;
        } catch (PDOException $e) {
            throw new AppException('Database connection lost');
        }
    }

    public function backup() {
        if (!is_dir(DB_BACKUP_DIR)) {
            mkdir(DB_BACKUP_DIR, 0777, true);
        }

        $filename = DB_BACKUP_DIR . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
        $command = sprintf(
            'mysqldump -h %s -u %s -p%s %s > %s',
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            $filename
        );

        exec($command, $output, $return);

        if ($return !== 0) {
            throw new AppException('Database backup failed');
        }

        if (DB_BACKUP_COMPRESS) {
            $zip = new \ZipArchive();
            $zipFile = $filename . '.zip';
            $zip->open($zipFile, \ZipArchive::CREATE);
            $zip->addFile($filename, basename($filename));
            $zip->close();
            unlink($filename);
            $filename = $zipFile;
        }

        $this->cleanupOldBackups();

        return $filename;
    }

    private function cleanupOldBackups() {
        $files = glob(DB_BACKUP_DIR . '/*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= DB_BACKUP_RETENTION * 86400) {
                    unlink($file);
                }
            }
        }
    }
}
