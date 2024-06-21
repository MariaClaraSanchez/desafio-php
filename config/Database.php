<?php

class Database {
    private static $instance = null;
    private $conn;
    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $_ENV['Host'] . ";port=" . $_ENV['Port'] . ";dbname=" . $_ENV['Name'], 
                $_ENV['User'], $_ENV['Pass']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexão bem-sucedida!";
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage() . " (Código de erro: " . $e->getCode() . ")";
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
