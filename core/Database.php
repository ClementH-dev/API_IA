<?php
namespace Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $config = include __DIR__ . '/../config/db.php';

            try {
                $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'];
                self::$instance = new PDO($dsn, $config['user'], $config['password']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Database connection error: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
