<?php

include('./config/Database.php');

/**
 * Classe que faz as controle de usuários e aplicação dos scripts sql no banco de dados
 */
class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Script para registrar novo usuário
     * 
     * @param array $data array cntendo o paylaod recebido, que vai conter os dados da transação
     */
    public function create($data) {
        try {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO " . $_ENV['TABLE_NAME_USERS'] . " (name, password, email, saldo) VALUES (:username, :password, :email, :saldo)");
            $stmt->bindParam(':username', $data['name']);
            $stmt->bindParam(':password', $hash);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':saldo', $data['saldo']);
            return $stmt->execute();
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao criar um usuário'],$e->getCode());
        }
    }

    /**
     * Script para consultar dados de um usuário
     * 
     * @param array $data array cntendo o paylaod recebido, que vai conter os dados da transação
     */
    public function getByUsername($data) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM " . $_ENV['TABLE_NAME_USERS'] . " WHERE name = :username OR email = :email");
            $stmt->bindParam(':username', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao buscar um usuário'],$e->getCode());
        }
    }
    
    /**
     * Script para consutlar o saldo de um usário específico 
     * 
     * @param int $userId id do usuário que será encontrado na tabela de usuários
     */
    public function getSaldo($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT saldo FROM " . $_ENV['TABLE_NAME_USERS'] . " WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao buscar saldo'],$e->getCode());
        }
    }

    /**
     *  Realizar update do saldo de um usuário
     * 
     * @param int $userId id do usuário que terá o update na tabela
     * @param int $valor valor da transferência
     */
    public function updateSaldo($userId, $valor) {
        try {
            $stmt = $this->conn->prepare("UPDATE " . $_ENV['TABLE_NAME_USERS'] . " SET saldo = :valor WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':valor', $valor);
            return $stmt->execute();
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao atualizar saldo'],$e->getCode());
        }
    }
}
?>
