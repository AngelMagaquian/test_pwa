<?php
// Configuración de la base de datos
return [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'dbname' => 'mensajes_db',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

class Database {
    private static $instance = null;
    private $conn;
    private $config;
    private $maxRetries = 3;
    private $retryDelay = 1; // segundos

    private function __construct() {
        $this->config = require __DIR__ . '/database.php';
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        $retries = 0;
        while ($retries < $this->maxRetries) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']}";
                $this->conn = new PDO($dsn, $this->config['user'], $this->config['pass'], $this->config['options']);
                return;
            } catch (PDOException $e) {
                $retries++;
                if ($retries === $this->maxRetries) {
                    throw new Exception("Error de conexión después de {$this->maxRetries} intentos: " . $e->getMessage());
                }
                sleep($this->retryDelay);
            }
        }
    }

    public function getConnection() {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->query($sql, array_values($data));
        return $this->getConnection()->lastInsertId();
    }
} 