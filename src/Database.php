<?php

namespace App;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private PDO $pdo;

    private function __construct() {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $options);
        } catch (PDOException $e) {
            // Geliştirme ortamında hatayı göster, üretimde logla
            // error_log('Database Connection Error: ' . $e->getMessage());
            throw new PDOException("Veritabanı bağlantı hatası: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = (new self())->pdo;
        }
        return self::$instance;
    }

    // Singleton pattern için klonlama ve unserialize engelleme (opsiyonel ama iyi pratik)
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
} 